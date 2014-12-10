<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer;

use Magento\Store\Model\Resource\Website\CollectionFactory as WebsiteCollectionFactory;
use Magento\Customer\Model\Resource\Group\CollectionFactory as GroupCollectionFactory;
use Magento\CatalogPermissions\Model\Permission;
use Magento\CatalogPermissions\App\ConfigInterface;
use Magento\Framework\StoreManagerInterface;
use Magento\Catalog\Model\Config as CatalogConfig;

abstract class AbstractAction
{
    /**#@+
     * Grant values for permissions
     */
    const GRANT_ALLOW = 1;

    const GRANT_DENY = 0;

    /**#@-*/

    /**
     * Category index table name
     */
    const INDEX_TABLE = 'magento_catalogpermissions_index';

    /**
     * Product index table name suffix
     */
    const PRODUCT_SUFFIX = '_product';

    /**
     * Suffix for index table to show it is temporary
     */
    const TMP_SUFFIX = '_tmp';

    /**
     * Category chunk size
     */
    const CATEGORY_STEP_COUNT = 500;

    /**
     * Product chunk size
     */
    const PRODUCT_STEP_COUNT = 1000000;

    /**
     * @var \Magento\Framework\App\Resource
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
     * @var int[]
     */
    protected $websitesIds = array();

    /**
     * @var int[]
     */
    protected $customerGroupIds = array();

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
    protected $indexCategoryPermissions = array();

    /**
     * Grant values for permission inheritance
     *
     * @var array
     */
    protected $grantsInheritance = array(
        'grant_catalog_category_view' => self::GRANT_DENY,
        'grant_catalog_product_price' => self::GRANT_ALLOW,
        'grant_checkout_items' => self::GRANT_ALLOW
    );

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\Config
     */
    protected $catalogConfig;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param WebsiteCollectionFactory $websiteCollectionFactory
     * @param GroupCollectionFactory $groupCollectionFactory
     * @param ConfigInterface $config
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Config $catalogConfig
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        WebsiteCollectionFactory $websiteCollectionFactory,
        GroupCollectionFactory $groupCollectionFactory,
        ConfigInterface $config,
        StoreManagerInterface $storeManager,
        CatalogConfig $catalogConfig
    ) {
        $this->resource = $resource;
        $this->websiteCollectionFactory = $websiteCollectionFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->catalogConfig = $catalogConfig;
    }

    /**
     * Retrieve connection for read data
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
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
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
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
     * Return category index table name
     *
     * @return string
     */
    protected function getIndexTable()
    {
        return $this->getTable(self::INDEX_TABLE);
    }

    /**
     * Return product index table
     *
     * @return string
     */
    protected function getProductIndexTable()
    {
        return $this->getIndexTable() . self::PRODUCT_SUFFIX;
    }

    /**
     * Return temporary category index table name
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
        return $this->useIndexTempTable ? $this->getTable(
            self::INDEX_TABLE . self::TMP_SUFFIX
        ) : $this->getIndexTable();
    }

    /**
     * Return temporary product index table name
     *
     * If 'useIndexTempTable' flag is true:
     *  - return temporary index table name.
     *
     * If 'useIndexTempTable' flag is false:
     *  - return index table name.
     *
     * @return string
     */
    protected function getProductIndexTempTable()
    {
        return $this->useIndexTempTable ? $this->getTable(
            self::INDEX_TABLE . self::PRODUCT_SUFFIX . self::TMP_SUFFIX
        ) : $this->getProductIndexTable();
    }

    /**
     * Retrieve list of customer group identifiers
     *
     * Return identifiers for all customer groups that exist in the system
     *
     * @return int[]
     */
    protected function getCustomerGroupIds()
    {
        if (!$this->customerGroupIds) {
            $this->customerGroupIds = $this->groupCollectionFactory->create()->getAllIds();
        }
        return $this->customerGroupIds;
    }

    /**
     * Retrieve list of website identifiers
     *
     * Return identifiers for all websites that exist in the system
     *
     * @return int[]
     */
    protected function getWebsitesIds()
    {
        if (!$this->websitesIds) {
            $this->websitesIds = $this->websiteCollectionFactory->create()->addFieldToFilter(
                'website_id',
                array('neq' => 0)
            )->getAllIds();
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
     * @param \Magento\Framework\DB\Select $select
     * @param string $field
     * @param int $stepCount
     * @return \Magento\Framework\DB\Select[]
     */
    protected function prepareSelectsByRange(
        \Magento\Framework\DB\Select $select,
        $field,
        $stepCount = self::CATEGORY_STEP_COUNT
    ) {
        return $this->isRangingNeeded() ? $this->getWriteAdapter()->selectsByRange(
            $field,
            $select,
            $stepCount
        ) : array(
            $select
        );
    }

    /**
     * Run reindexation
     *
     * @return void
     */
    protected function reindex()
    {
        $categoryList = $this->getCategoryList();

        $permissions = $this->getCategoryPermissions(array_keys($categoryList));
        foreach ($permissions as $permission) {
            $this->prepareCategoryIndexPermissions($permission, $categoryList[$permission['category_id']]);
        }

        foreach ($categoryList as $categoryId => $path) {
            $this->prepareInheritedCategoryIndexPermissions($categoryId, $path);
        }

        $this->populateCategoryIndex();

        $this->populateProductIndex();
        $this->fixProductPermissions();
    }

    /**
     * Retrieve category list
     *
     * Returns [entity_id, path] pairs.
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
    protected function getCategoryPermissions(array $entityIds)
    {
        $grants = array();
        foreach (array_keys($this->grantsInheritance) as $grant) {
            $grants[] = $this->getReadAdapter()->quoteInto(
                sprintf('permission.%s != ?', $grant),
                Permission::PERMISSION_PARENT
            );
        }

        $select = $this->getReadAdapter()->select()->from(
            array('permission' => $this->getTable('magento_catalogpermissions')),
            array(
                'category_id',
                'website_id',
                'customer_group_id',
                'grant_catalog_category_view',
                'grant_catalog_product_price',
                'grant_checkout_items'
            )
        )->where(
            '(' . implode(' OR ', $grants) . ')'
        )->order(
            array('category_id', 'website_id', 'customer_group_id')
        );

        if (!empty($entityIds)) {
            $select->where('permission.category_id IN (?)', $entityIds);
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
    protected function prepareCategoryIndexPermissions(array $permission, $path)
    {
        $websiteIds = is_null($permission['website_id']) ? $this->getWebsitesIds() : array($permission['website_id']);

        $customerGroupIds = is_null(
            $permission['customer_group_id']
        ) ? $this->getCustomerGroupIds() : array(
            $permission['customer_group_id']
        );

        foreach ($websiteIds as $websiteId) {
            foreach ($customerGroupIds as $customerGroupId) {
                $permission['website_id'] = $websiteId;
                $permission['customer_group_id'] = $customerGroupId;
                $this->indexCategoryPermissions[$path][$websiteId . '_' . $customerGroupId] = $permission;
            }
        }
    }

    /**
     * Prepare grants for certain category path
     *
     * @param string $path
     * @return void
     */
    protected function prepareCategoryInheritance($path)
    {
        $parentPath = substr($path, 0, strrpos($path, '/'));
        foreach (array_keys($this->indexCategoryPermissions[$path]) as $uniqKey) {
            if (isset($this->indexCategoryPermissions[$parentPath][$uniqKey])) {
                foreach ($this->grantsInheritance as $grant => $inheritance) {
                    $value = $this->indexCategoryPermissions[$parentPath][$uniqKey][$grant];
                    if ($this->indexCategoryPermissions[$path][$uniqKey][$grant] == Permission::PERMISSION_PARENT) {
                        $this->indexCategoryPermissions[$path][$uniqKey][$grant] = $value;
                    } else {
                        if ($inheritance == self::GRANT_ALLOW) {
                            $value = max($this->indexCategoryPermissions[$path][$uniqKey][$grant], $value);
                        }
                        $value = min($this->indexCategoryPermissions[$path][$uniqKey][$grant], $value);
                        $this->indexCategoryPermissions[$path][$uniqKey][$grant] = $value;
                    }
                    if ($this->indexCategoryPermissions[$path][$uniqKey][$grant] == Permission::PERMISSION_PARENT) {
                        $this->indexCategoryPermissions[$path][$uniqKey][$grant] = null;
                    }
                }
            }
        }
    }

    /**
     * Inherit category permission from it's parent
     *
     * @param int $categoryId
     * @param string $path
     * @return void
     */
    protected function prepareInheritedCategoryIndexPermissions($categoryId, $path)
    {
        $parentPath = substr($path, 0, strrpos($path, '/'));

        if (isset($this->indexCategoryPermissions[$path])) {
            $this->prepareCategoryInheritance($path);
            if (isset($this->indexCategoryPermissions[$parentPath])) {
                foreach (array_keys($this->indexCategoryPermissions[$parentPath]) as $uniqKey) {
                    if (!isset($this->indexCategoryPermissions[$path][$uniqKey])) {
                        $this->indexCategoryPermissions[$path][$uniqKey] = $this
                            ->indexCategoryPermissions[$parentPath][$uniqKey];
                    }
                }
            }
        } elseif (isset($this->indexCategoryPermissions[$parentPath])) {
            foreach ($this->indexCategoryPermissions[$parentPath] as $uniqKey => $permission) {
                $this->indexCategoryPermissions[$path][$uniqKey] = array_merge(
                    $permission,
                    array('category_id' => $categoryId)
                );
            }
        }
    }

    /**
     * Populate main index table with prepared permissions
     *
     * @return void
     */
    protected function populateCategoryIndex()
    {
        foreach ($this->indexCategoryPermissions as $permissions) {
            foreach ($permissions as $permission) {
                if ($permission['grant_catalog_category_view'] == Permission::PERMISSION_DENY) {
                    $permission['grant_catalog_product_price'] = Permission::PERMISSION_DENY;
                }
                if ($permission['grant_catalog_product_price'] == Permission::PERMISSION_DENY) {
                    $permission['grant_checkout_items'] = Permission::PERMISSION_DENY;
                }

                $this->getWriteAdapter()->insertOnDuplicate(
                    $this->getIndexTempTable(),
                    array(
                        'category_id' => $permission['category_id'],
                        'website_id' => $permission['website_id'],
                        'customer_group_id' => $permission['customer_group_id'],
                        'grant_catalog_category_view' => $permission['grant_catalog_category_view'],
                        'grant_catalog_product_price' => $permission['grant_catalog_product_price'],
                        'grant_checkout_items' => $permission['grant_checkout_items']
                    )
                );
            }
        }
    }

    /**
     * Get permissions columns
     *
     * @return array
     */
    protected function getPermissionColumns()
    {
        $grantView = $this->getConfigGrantDbExpr(
            $this->config->getCatalogCategoryViewMode(),
            $this->config->getCatalogCategoryViewGroups()
        );
        $grantPrice = $this->getConfigGrantDbExpr(
            $this->config->getCatalogProductPriceMode(),
            $this->config->getCatalogProductPriceGroups()
        );
        $grantCheckout = $this->getConfigGrantDbExpr(
            $this->config->getCheckoutItemsMode(),
            $this->config->getCheckoutItemsGroups()
        );

        $adapter = $this->getReadAdapter();

        $exprCatalogCategoryView = $adapter->getCheckSql(
            $adapter->quoteInto('grant_catalog_category_view = ?', Permission::PERMISSION_PARENT),
            'NULL',
            'grant_catalog_category_view'
        );
        $exprCatalogProductPrice = $adapter->getCheckSql(
            $adapter->quoteInto('grant_catalog_product_price = ?', Permission::PERMISSION_PARENT),
            'NULL',
            'grant_catalog_product_price'
        );
        $exprCheckoutItems = $adapter->getCheckSql(
            $adapter->quoteInto('grant_checkout_items = ?', Permission::PERMISSION_PARENT),
            'NULL',
            'grant_checkout_items'
        );

        return array(
            'grant_catalog_category_view' => 'MAX(' . $adapter->getCheckSql(
                $adapter->quoteInto('? IS NULL', $exprCatalogCategoryView),
                $adapter->quoteInto('?', $grantView),
                $adapter->quoteInto('?', $exprCatalogCategoryView)
            ) . ')',
            'grant_catalog_product_price' => 'MAX(' . $adapter->getCheckSql(
                $adapter->quoteInto('? IS NULL', $exprCatalogProductPrice),
                $adapter->quoteInto('?', $grantPrice),
                $adapter->quoteInto('?', $exprCatalogProductPrice)
            ) . ')',
            'grant_checkout_items' => 'MAX(' . $adapter->getCheckSql(
                $adapter->quoteInto('? IS NULL', $exprCheckoutItems),
                $adapter->quoteInto('?', $grantCheckout),
                $adapter->quoteInto('?', $exprCheckoutItems)
            ) . ')'
        );
    }

    /**
     * Create select for populating product index
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function createProductSelect()
    {
        $statusAttributeId = $this->catalogConfig->getAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'status'
        )->getId();
        $visibilityAttributeId = $this->catalogConfig->getAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'visibility'
        )->getId();
        $isActiveAttributeId = $this->catalogConfig->getAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'is_active'
        )->getId();


        $select = $this->getReadAdapter()->select()->from(
            array('category_product' => $this->getTable('catalog_category_product')),
            array()
        )->columns(
            array_merge(
                array('category_product.product_id', 'store.store_id', 'customer_group.customer_group_id'),
                $this->getPermissionColumns()
            )
        )->joinInner(
            array('product_website' => $this->getTable('catalog_product_website')),
            'product_website.product_id = category_product.product_id',
            array()
        )->joinInner(
            array('store_group' => $this->getTable('store_group')),
            'store_group.website_id = product_website.website_id',
            array()
        )->joinInner(
            array('store' => $this->getTable('store')),
            'store.website_id = product_website.website_id' . ' AND store.group_id = store_group.group_id',
            array()
        )->joinInner(
            array('category' => $this->getTable('catalog_category_entity')),
            'category.entity_id = category_product.category_id' .
            ' AND category.path LIKE ' .
            $this->getReadAdapter()->getConcatSql(
                array(
                    $this->getReadAdapter()->quote(\Magento\Catalog\Model\Category::TREE_ROOT_ID . '/'),
                    $this->getReadAdapter()->quoteIdentifier('store_group.root_category_id'),
                    $this->getReadAdapter()->quote('/%')
                )
            ),
            array()
        )->joinInner(
            array('cpsd' => $this->getTable('catalog_product_entity_int')),
            'cpsd.entity_id = category_product.product_id AND cpsd.store_id = 0' . $this->getReadAdapter()->quoteInto(
                ' AND cpsd.attribute_id = ?',
                $statusAttributeId
            ),
            array()
        )->joinLeft(
            array('cpss' => $this->getTable('catalog_product_entity_int')),
            'cpss.entity_id = category_product.product_id AND cpss.attribute_id = cpsd.attribute_id' .
            ' AND cpss.store_id = store.store_id',
            array()
        )->joinInner(
            array('cpvd' => $this->getTable('catalog_product_entity_int')),
            'cpvd.entity_id = category_product.product_id AND cpvd.store_id = 0' . $this->getReadAdapter()->quoteInto(
                ' AND cpvd.attribute_id = ?',
                $visibilityAttributeId
            ),
            array()
        )->joinLeft(
            array('cpvs' => $this->getTable('catalog_product_entity_int')),
            'cpvs.entity_id = category_product.product_id AND cpvs.attribute_id = cpvd.attribute_id' .
            ' AND cpvs.store_id = store.store_id',
            array()
        )->joinInner(
            array('ccad' => $this->getTable('catalog_category_entity_int')),
            'ccad.entity_id = category_product.category_id AND ccad.store_id = 0' . $this->getReadAdapter()->quoteInto(
                ' AND ccad.attribute_id = ?',
                $isActiveAttributeId
            ),
            array()
        )->joinLeft(
            array('ccas' => $this->getTable('catalog_category_entity_int')),
            'ccas.entity_id = category_product.category_id AND ccas.attribute_id = ccad.attribute_id' .
            ' AND ccas.store_id = store.store_id',
            array()
        )->joinInner(
            array('customer_group' => $this->getTable('customer_group')),
            '',
            array()
        )->joinInner(
            array('permission_index' => $this->getIndexTempTable()),
            'permission_index.category_id = category_product.category_id' .
            ' AND permission_index.website_id = product_website.website_id' .
            ' AND permission_index.customer_group_id = customer_group.customer_group_id',
            array()
        )->where(
            $this->getReadAdapter()->getIfNullSql('cpss.value', 'cpsd.value') . ' = ?',
            \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
        )->where(
            $this->getReadAdapter()->getIfNullSql('cpvs.value', 'cpvd.value') . ' IN (?)',
            array(
                \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG,
                \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_SEARCH,
                \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
            )
        )->where(
            $this->getReadAdapter()->getIfNullSql('ccas.value', 'ccad.value') . ' = ?',
            1
        )->group(
            array('store.store_id', 'category_product.product_id', 'customer_group.customer_group_id')
        );

        if ($this->getProductList()) {
            $select->where('category_product.product_id IN (?)', $this->getProductList());
        }

        return $select;
    }

    /**
     * Return list of product IDs to reindex
     *
     * @return int[]
     */
    abstract protected function getProductList();

    /**
     * Populate product index
     *
     * @return $this
     */
    protected function populateProductIndex()
    {
        $selects = $this->prepareSelectsByRange($this->createProductSelect(), 'product_id', self::PRODUCT_STEP_COUNT);

        foreach ($selects as $select) {
            $this->getWriteAdapter()->query(
                $this->getWriteAdapter()->insertFromSelect(
                    $select,
                    $this->getProductIndexTempTable(),
                    array(
                        'product_id',
                        'store_id',
                        'customer_group_id',
                        'grant_catalog_category_view',
                        'grant_catalog_product_price',
                        'grant_checkout_items'
                    ),
                    \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
                )
            );
        }

        return $this;
    }

    /**
     * Fix product permissions after population
     *
     * @return $this
     */
    protected function fixProductPermissions()
    {
        $deny = (int)Permission::PERMISSION_DENY;
        $data = array(
            'grant_catalog_product_price' => $this->getReadAdapter()->getCheckSql(
                $this->getReadAdapter()->quoteInto('grant_catalog_category_view = ?', $deny),
                $deny,
                'grant_catalog_product_price'
            ),
            'grant_checkout_items' => $this->getReadAdapter()->getCheckSql(
                $this->getReadAdapter()->quoteInto(
                    'grant_catalog_category_view = ?',
                    $deny
                ) . ' OR ' . $this->getReadAdapter()->quoteInto(
                    'grant_catalog_product_price = ?',
                    $deny
                ),
                $deny,
                'grant_checkout_items'
            )
        );

        $condition = $this->getProductList() ? array('product_id IN (?)' => $this->getProductList()) : '';

        $this->getWriteAdapter()->update($this->getProductIndexTempTable(), $data, $condition);

        return $this;
    }

    /**
     * Generates CASE ... WHEN .... THEN expression for grant depends on config
     *
     * @param string $mode
     * @param string[] $groups
     * @return \Zend_Db_Expr
     */
    protected function getConfigGrantDbExpr($mode, $groups)
    {
        $result = new \Zend_Db_Expr('0');
        $conditions = array();
        $readAdapter = $this->getReadAdapter();

        foreach ($this->storeManager->getStores() as $store) {
            if ($mode == ConfigInterface::GRANT_CUSTOMER_GROUP) {
                foreach ($groups as $groupId) {
                    if (is_numeric($groupId)) {
                        // Case per customer group
                        $condition = $readAdapter->quoteInto(
                            'store.store_id = ?',
                            $store->getId()
                        ) . ' AND ' . $readAdapter->quoteInto(
                            'customer_group.customer_group_id = ?',
                            (int)$groupId
                        );
                        $conditions[$condition] = Permission::PERMISSION_ALLOW;
                    }
                }

                $condition = $readAdapter->quoteInto('store.store_id = ?', $store->getId());
                $conditions[$condition] = Permission::PERMISSION_DENY;
            } else {
                $condition = $readAdapter->quoteInto('store.store_id = ?', $store->getId());
                $conditions[$condition] = $mode !=
                    ConfigInterface::GRANT_NONE ? Permission::PERMISSION_ALLOW : Permission::PERMISSION_DENY;
            }
        }

        if (!empty($conditions)) {
            $result = $readAdapter->getCaseSql('', $conditions);
        }

        return $result;
    }
}
