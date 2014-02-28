<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\CatalogPermissions\Model\Indexer\Category;

use \Magento\Core\Model\Resource\Website\CollectionFactory as WebsiteCollectionFactory;
use \Magento\Customer\Model\Resource\Group\CollectionFactory as GroupCollectionFactory;
use \Magento\CatalogPermissions\Model\Permission;

abstract class AbstractAction
{
    /**#@+
     * Grant values for permissions
     */
    const GRANT_ALLOW = 1;
    const GRANT_DENY = 0;
    /**#@-*/

    /**
     * Index table name
     */
    const INDEX_TABLE = 'magento_catalogpermissions_index';

    /**
     * Suffix for index table to show it is temporary
     */
    const TMP_SUFFIX = '_tmp';

    /**
     * Chunk size
     */
    const DEFAULT_STEP_COUNT = 500;

    /**
     * @var \Magento\App\Resource
     */
    protected $resource;

    /**
     * @var WebsiteCollectionFactory
     */
    protected $websiteCollectionFactory;

    /**
     * @var GroupCollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var array
     */
    protected $websitesIds = [];

    /**
     * @var array
     */
    protected $customerGroupIds = [];

    /**
     * Whether to use index or temporary index table
     *
     * @var bool
     */
    protected $useIndexTempTable = true;

    /**
     * List of permissions prepared to insert into index
     *
     * @var array
     */
    protected $indexPermissions = [];

    /**
     * Grant values for permission inheritance
     *
     * @var array
     */
    protected $grantsInheritance  = [
        'grant_catalog_category_view' => self::GRANT_DENY,
        'grant_catalog_product_price' => self::GRANT_ALLOW,
        'grant_checkout_items' => self::GRANT_ALLOW,
    ];

    /**
     * @param \Magento\App\Resource $resource
     * @param WebsiteCollectionFactory $websiteCollectionFactory
     * @param GroupCollectionFactory $groupCollectionFactory
     */
    public function __construct(
        \Magento\App\Resource $resource,
        WebsiteCollectionFactory $websiteCollectionFactory,
        GroupCollectionFactory $groupCollectionFactory
    ) {
        $this->resource = $resource;
        $this->websiteCollectionFactory = $websiteCollectionFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
    }

    /**
     * Retrieve connection for read data
     *
     * @return \Magento\DB\Adapter\AdapterInterface
     */
    protected function getReadAdapter()
    {
        $writeAdapter = $this->getWriteAdapter();
        if ($writeAdapter && $writeAdapter->getTransactionLevel() > 0) {
            // if transaction is started we should use write connection for reading
            return $writeAdapter;
        }
        return $this->resource->getConnection('read');
    }

    /**
     * Retrieve connection for write data
     *
     * @return \Magento\DB\Adapter\AdapterInterface
     */
    protected function getWriteAdapter()
    {
        return $this->resource->getConnection('write');
    }

    /**
     * Return validated table name
     *
     * @param string|string[] $table
     * @return string
     */
    protected function getTable($table)
    {
        return $this->resource->getTableName($table);
    }

    /**
     * Return main index table name
     *
     * @return string
     */
    protected function getIndexTable()
    {
        return $this->getTable(self::INDEX_TABLE);
    }

    /**
     * Return temporary index table name
     *
     * If 'useIndexTempTable' flag is true:
     *  - return temporary index table name.
     *
     * If 'useIndexTempTable' flag is false:
     *  - return index table name.
     *
     * @return string
     */
    protected function getIndexTempTable()
    {
        return $this->useIndexTempTable
            ? $this->getTable(self::INDEX_TABLE . self::TMP_SUFFIX)
            : $this->getIndexTable();
    }

    /**
     * Retrieve list of customer group identifiers
     *
     * Return identifiers for all customer groups that exist in the system
     *
     * @return array
     */
    protected function getCustomerGroupIds()
    {
        if (!$this->customerGroupIds) {
            $this->customerGroupIds = $this->groupCollectionFactory->create()
                ->getAllIds();
        }
        return $this->customerGroupIds;
    }

    /**
     * Retrieve list of website identifiers
     *
     * Return identifiers for all websites that exist in the system
     *
     * @return array
     */
    protected function getWebsitesIds()
    {
        if (!$this->websitesIds) {
            $this->websitesIds = $this->websiteCollectionFactory->create()
                ->addFieldToFilter('website_id', ['neq' => 0])
                ->getAllIds();
        }
        return $this->websitesIds;
    }

    /**
     * Check whether select ranging is needed
     *
     * @return bool
     */
    abstract protected function isRangingNeeded();

    /**
     * Return selects cut by min and max
     *
     * @param \Magento\DB\Select $select
     * @param string $field
     * @param int $stepCount
     * @return \Magento\DB\Select[]
     */
    protected function prepareSelectsByRange(\Magento\DB\Select $select, $field, $stepCount = self::DEFAULT_STEP_COUNT)
    {
        return $this->isRangingNeeded()
            ? $this->getWriteAdapter()->selectsByRange($field, $select, $stepCount)
            : [$select];
    }

    /**
     * Run reindexation
     *
     * @return void
     */
    protected function reindex()
    {
        $categoryList = $this->getCategoryList();

        $permissions = $this->getPermissions(array_keys($categoryList));
        foreach ($permissions as $permission) {
            $this->prepareIndexPermissions($permission, $categoryList[$permission['category_id']]);
        }

        foreach ($categoryList as $path) {
            $this->prepareInheritedIndexPermissions($path);
        }

        $this->populateIndex();
    }

    /**
     * Retrieve category list
     *
     * Return entity_id, path pairs.
     *
     * @return array
     */
    abstract protected function getCategoryList();

    /**
     * Retrieve permissions assigned to categories
     *
     * @param int[] $entityIds
     * @return array
     */
    protected function getPermissions(array $entityIds)
    {
        $grants = [];
        foreach (array_keys($this->grantsInheritance) as $grant) {
            $grants[] = $this->getReadAdapter()->quoteInto(
                sprintf('permission.%s != ?', $grant),
                Permission::PERMISSION_PARENT
            );
        }

        $select = $this->getReadAdapter()->select()
            ->from(
                ['permission' => $this->getTable('magento_catalogpermissions')],
                [
                    'category_id',
                    'website_id',
                    'customer_group_id',
                    'grant_catalog_category_view',
                    'grant_catalog_product_price',
                    'grant_checkout_items'
                ]
            )
            ->where('(' . implode(' OR ', $grants).  ')')
            ->order(['category_id', 'website_id', 'customer_group_id']);

        if (!empty($entityIds)) {
            $select ->where('permission.category_id IN (?)', $entityIds);
        }

        return $this->getReadAdapter()->fetchAll($select);
    }

    /**
     * Prepare list of permissions for certain category path
     *
     * @param array $permission
     * @param string $path
     * @return void
     */
    protected function prepareIndexPermissions(array $permission, $path)
    {
        $websiteIds = is_null($permission['website_id'])
            ? $this->getWebsitesIds() : [$permission['website_id']];

        $customerGroupIds = is_null($permission['customer_group_id'])
            ? $this->getCustomerGroupIds() : [$permission['customer_group_id']];

        foreach ($websiteIds as $websiteId) {
            foreach ($customerGroupIds as $customerGroupId) {
                $permission['website_id'] = $websiteId;
                $permission['customer_group_id'] = $customerGroupId;
                $this->indexPermissions[$path][$websiteId . '_' . $customerGroupId] = $permission;
            }
        }
    }

    /**
     * Inherit category permission from it's parent
     *
     * @param string $path
     * @return void
     */
    protected function prepareInheritedIndexPermissions($path)
    {
        $parentPath = substr($path, 0, strrpos($path, '/'));

        if (isset($this->indexPermissions[$path])) {
            foreach (array_keys($this->indexPermissions[$path]) as $uniqKey) {
                if (isset($this->indexPermissions[$parentPath][$uniqKey])) {
                    foreach ($this->grantsInheritance as $grant => $inheritance) {
                        $value = $this->indexPermissions[$parentPath][$uniqKey][$grant];
                        if ($this->indexPermissions[$path][$uniqKey][$grant] == Permission::PERMISSION_PARENT) {
                            $this->indexPermissions[$path][$uniqKey][$grant] = $value;
                        } else {
                            if ($inheritance == self::GRANT_ALLOW) {
                                $value = max($this->indexPermissions[$path][$uniqKey][$grant], $value);
                            }
                            $value = min($this->indexPermissions[$path][$uniqKey][$grant], $value);
                            $this->indexPermissions[$path][$uniqKey][$grant] = $value;
                        }
                        if ($this->indexPermissions[$path][$uniqKey][$grant] == Permission::PERMISSION_PARENT) {
                            $this->indexPermissions[$path][$uniqKey][$grant] = null;
                        }
                    }
                }
            }

            if (isset($this->indexPermissions[$parentPath])) {
                foreach (array_keys($this->indexPermissions[$parentPath]) as $uniqKey) {
                    if (!isset($this->indexPermissions[$path][$uniqKey])) {
                        $this->indexPermissions[$path][$uniqKey] = $this->indexPermissions[$parentPath][$uniqKey];
                    }
                }
            }

        } elseif (isset($this->indexPermissions[$parentPath])) {
            $this->indexPermissions[$path] = $this->indexPermissions[$parentPath];
        }
    }

    /**
     * Populate main index table with prepared permissions
     *
     * @return void
     */
    protected function populateIndex()
    {
        foreach ($this->indexPermissions as $permissions) {
            foreach ($permissions as $permission) {
                if ($permission['grant_catalog_category_view'] == Permission::PERMISSION_DENY) {
                    $permission['grant_catalog_product_price'] = Permission::PERMISSION_DENY;
                }
                if ($permission['grant_catalog_product_price'] == Permission::PERMISSION_DENY) {
                    $permission['grant_checkout_items'] = Permission::PERMISSION_DENY;
                }

                $this->getWriteAdapter()->insert($this->getIndexTempTable(), [
                    'category_id'                 => $permission['category_id'],
                    'website_id'                  => $permission['website_id'],
                    'customer_group_id'           => $permission['customer_group_id'],
                    'grant_catalog_category_view' => $permission['grant_catalog_category_view'],
                    'grant_catalog_product_price' => $permission['grant_catalog_product_price'],
                    'grant_checkout_items'        => $permission['grant_checkout_items']
                ]);
            }
        }
    }
}
