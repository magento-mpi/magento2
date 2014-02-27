<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogPermissions\Model\Indexer\Category\Action;

use \Magento\CatalogPermissions\Model\Indexer\Category\AbstractAction;
use \Magento\CatalogPermissions\Model\Permission;

class Full extends AbstractAction
{
    /**#@+
     * Grant values for permissions
     */
    const GRANT_ALLOW = 1;
    const GRANT_DENY = 0;
    /**#@-*/

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
     * List of permissions prepared to insert into index
     *
     * @var array
     */
    protected $indexPermissions = [];

    /**
     * Refresh entities index
     *
     * @return $this
     */
    public function execute()
    {
        $this->clearIndexTmpData();

        $this->reindex();

        $this->publishData();
        $this->removeUnnecessaryData();

        return $this;
    }

    /**
     * Run full reindexation
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
     * If 'path' has a value:
     *  - return current category;
     *  - return all child categories;
     *  - return all parent categories.
     *
     * If 'path' hasn't value:
     *  - return all categories.
     *
     * @param string|null $path
     * @return array
     */
    protected function getCategoryList($path = null)
    {
        $select = $this->getReadAdapter()->select()
            ->from($this->getTable('catalog_category_entity'), ['entity_id', 'path'])
            ->order('level ASC');

        if (!is_null($path)) {
            $select->where('path LIKE ?', $path . '/%')
                ->orWhere('entity_id IN(?)', explode('/', $path));
        }

        return $this->getReadAdapter()->fetchPairs($select);
    }

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
    protected function prepareIndexPermissions($permission, $path)
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

                $this->getWriteAdapter()->insert($this->getIndexTmpTable(), [
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

    /**
     * Clear all index temporary data
     *
     * @return void
     */
    protected function clearIndexTmpData()
    {
        $this->getWriteAdapter()->delete(
            $this->getIndexTmpTable()
        );
    }

    /**
     * Publish data from temporary index to index
     *
     * @return void
     */
    protected function publishData()
    {
        $select = $this->getWriteAdapter()->select()
            ->from($this->getIndexTmpTable());

        $queries = $this->prepareSelectsByRange($select, 'category_id');

        foreach ($queries as $query) {
            $this->getWriteAdapter()->query(
                $this->getWriteAdapter()->insertFromSelect(
                    $query,
                    $this->getIndexTable(),
                    ['category_id', 'website_id', 'customer_group_id',
                        'grant_catalog_category_view', 'grant_catalog_product_price', 'grant_checkout_items'],
                    \Magento\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
                )
            );
        }
    }

    /**
     * Remove unnecessary data
     *
     * @return void
     */
    protected function removeUnnecessaryData()
    {
        $query = $this->getWriteAdapter()->select()
            ->from(['m' => $this->getIndexTable()])
            ->joinLeft(
                ['t' => $this->getIndexTmpTable()],
                'm.category_id = t.category_id'
                . ' AND m.website_id = t.website_id'
                . ' AND m.customer_group_id = t.customer_group_id'
            )
            ->where('t.category_id IS NULL');

        $this->getWriteAdapter()->query(
            $this->getWriteAdapter()->deleteFromSelect($query, $this->getIndexTable())
        );
    }
}
