<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogPermissions
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Permission indexer resource
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogPermissions
 */
class Enterprise_CatalogPermissions_Model_Mysql4_Permission_Index extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Data for insert
     *
     * @var array
     */
    protected $_insertData = array();

    /**
     * Table fields for insert
     *
     * @var array
     */
    protected $_tableFields = array();

    /**
     * Permission cache
     *
     * @var array
     */
    protected $_permissionCache = array();

    /**
     * Inheritance of grant appling in categories tree
     *
     * @return array
     */
    protected $_grantsInheritance = array(
        'grant_catalog_category_view' => 'deny',
        'grant_catalog_product_price' => 'allow',
        'grant_checkout_items' => 'allow'
    );

    protected function _construct()
    {
        $this->_init('enterprise_catalogpermissions/permission_index', 'category_id');
    }



    /**
     * Reindex category permissions
     *
     * @param string $categoryPath
     * @return Enterprise_CatalogPermissions_Model_Mysql4_Permission_Indexer
     */
    public function reindex($categoryPath)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('catalog/category'), array('entity_id','path'))
            ->where('path LIKE ?', $categoryPath . '/%')
            ->orWhere('entity_id IN(?)', explode('/', $categoryPath))
            ->order('level ASC');

        $categoryPath = $this->_getReadAdapter()->fetchPairs($select);
        $categoryIds = array_keys($categoryPath);

        $select = $this->_getReadAdapter()->select()
            ->from(array('permission' => $this->getTable('enterprise_catalogpermissions/permission')), array(
                'category_id',
                'website_id',
                'customer_group_id',
                'grant_catalog_category_view',
                'grant_catalog_product_price',
                'grant_checkout_items'
            ))
            ->where('permission.category_id IN (?)', $categoryIds);

        $notEmptyWhere = array();

        foreach (array_keys($this->_grantsInheritance) as $grant) {
             $notEmptyWhere[] = 'permission.' . $grant . ' != 0';
        }

        $select->where('(' . implode(' OR ', $notEmptyWhere).  ')');

        $permissions = $this->_getReadAdapter()->fetchAll($select);

        // Delete old index
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            $this->_getWriteAdapter()->quoteInto('category_id IN (?)', $categoryIds)
        );

        $this->_permissionCache = array();

        foreach ($permissions as $permission) {
            $uniqKey = $permission['website_id']
                     . '_' . $permission['customer_group_id'];
            $path = $categoryPath[$permission['category_id']];
            $this->_permissionCache[$path][$uniqKey] = $permission;
        }

        unset ($permissions);

        $fields =  array_merge(
            array(
                'category_id', 'website_id', 'customer_group_id',
                'grant_catalog_category_view',
                'grant_catalog_product_price',
                'grant_checkout_items'
            )
        );

        $this->_beginInsert('permission_index', $fields);

        foreach ($categoryPath as $categoryId => $path) {
            $this->_inheritCategoryPermission($path);
            if (isset($this->_permissionCache[$path])) {
                foreach ($this->_permissionCache[$path] as $permission) {
                    if ($permission['grant_catalog_category_view'] == -2) {
                        $permission['grant_catalog_product_price'] = - 2;
                    }
                    if ($permission['grant_catalog_product_price'] == -2) {
                        $permission['grant_checkout_items'] = -2;
                    }
                    $this->_insert('permission_index', array(
                        $categoryId,
                        $permission['website_id'],
                        $permission['customer_group_id'],
                        $permission['grant_catalog_category_view'],
                        $permission['grant_catalog_product_price'],
                        $permission['grant_checkout_items']
                    ));
                }
            }
        }

        $this->_commitInsert('permission_index');
        $this->_permissionCache = array();

        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('catalog/category_product'), 'product_id')
            ->distinct(true)
            ->where('category_id IN(?)', $categoryIds);

        $productIds = $this->_getReadAdapter()->fetchCol($select);

        $this->reindexProducts($productIds);

        return $this;
    }

    /**
     * Reindex products permissions
     *
     * @param array|string $productIds
     * @return Enterprise_CatalogPermissions_Model_Mysql4_Permission_Index
     */
    public function reindexProducts($productIds = null)
    {
        $isActiveAttributeId = Mage::getSingleton('eav/config')->getAttribute('catalog_category', 'is_active')->getId();

        $select = $this->getReadConnection()->select()
            ->from(
                array('category_product_index' => $this->getTable('catalog/category_product_index')),
                array('product_id', 'store_id')
            )->joinLeft(
                array('category_is_active' => $this->getTable('catalog/category') . '_int'),
                'category_product_index.category_id = category_is_active.entity_id AND
                    category_is_active.store_id = category_product_index.store_id
                 AND category_is_active.attribute_id = ' . (int) $isActiveAttributeId,
                array()
            )->joinLeft(
                array('category_is_active_default' => $this->getTable('catalog/category') . '_int'),
                'category_product_index.category_id = category_is_active_default.entity_id AND
                    category_is_active_default.store_id = 0
                  AND category_is_active_default.attribute_id = ' . (int) $isActiveAttributeId,
                array()
            )->join(
                array('store' => $this->getTable('core/store')),
                'category_product_index.store_id = store.store_id',
                array()
            )->group(array(
                'category_product_index.store_id',
                'category_product_index.product_id',
                'permission_index.customer_group_id'
           ))->where('IFNULL(category_is_active.value, category_is_active_default.value) = 1');

        $selectCategory = clone $select;

        $select
            ->columns(array('category_id'=>new Zend_Db_Expr('NULL')), 'category_product_index')
            ->join(
                array('permission_index'=>$this->getTable('permission_index')),
                'category_product_index.category_id = permission_index.category_id AND
                 store.website_id = permission_index.website_id',
                array(
                    'customer_group_id',
                    'grant_catalog_category_view' => 'MAX(IF(permission_index.grant_catalog_category_view = 0, NULL, permission_index.grant_catalog_category_view))',
                    'grant_catalog_product_price' => 'MAX(IF(permission_index.grant_catalog_product_price = 0, NULL, permission_index.grant_catalog_product_price))',
                    'grant_checkout_items' => 'MAX(IF(permission_index.grant_checkout_items = 0, NULL, permission_index.grant_checkout_items))'
                )
            )->where('category_product_index.is_parent = ?', 1);

        // Select for per category product index (without anchor category usage)
        $selectCategory
             ->columns('category_id', 'category_product_index')
             ->join(
                array('permission_index'=>$this->getTable('permission_index')),
                'category_product_index.category_id = permission_index.category_id AND
                 store.website_id = permission_index.website_id',
                array(
                    'customer_group_id',
                    'grant_catalog_category_view' => 'MAX(IF(permission_index.grant_catalog_category_view = 0, NULL, permission_index.grant_catalog_category_view))',
                    'grant_catalog_product_price' => 'MAX(IF(permission_index.grant_catalog_product_price = 0, NULL, permission_index.grant_catalog_product_price))',
                    'grant_checkout_items' => 'MAX(IF(permission_index.grant_checkout_items = 0, NULL, permission_index.grant_checkout_items))'
                )
            )->group('category_product_index.category_id')
            ->where('category_product_index.is_parent = ?', 1);

        // Select for per category product index (with anchor category)
        $selectAnchorCategory = $this->_getReadAdapter()->select();
        $selectAnchorCategory
            ->from(array('permission_index_product'=>$this->getTable('permission_index_product')),
                array(
                    'product_id',
                    'store_id'
                )
            )->join(
                array('category_product_index' => $this->getTable('catalog/category_product_index')),
                'permission_index_product.product_id = category_product_index.product_id',
                array('category_id')
            )->join(
                array('category'=>$this->getTable('catalog/category')),
                'category.entity_id = category_product_index.category_id',
                array()
            )->join(
                array('category_child'=>$this->getTable('catalog/category')),
                'category_child.path LIKE CONCAT(category.path, \'/%\')
                AND category_child.entity_id = permission_index_product.category_id',
                array()
            )->columns(
                array(
                    'customer_group_id',
                    'grant_catalog_category_view' => 'MAX(IF(permission_index_product.grant_catalog_category_view = 0, NULL, permission_index_product.grant_catalog_category_view))',
                    'grant_catalog_product_price' => 'MAX(IF(permission_index_product.grant_catalog_product_price = 0, NULL, permission_index_product.grant_catalog_product_price))',
                    'grant_checkout_items' => 'MAX(IF(permission_index_product.grant_checkout_items = 0, NULL, permission_index_product.grant_checkout_items))'
                ),
                'permission_index_product'
           )->group(array(
                'permission_index_product.store_id',
                'permission_index_product.product_id',
                'permission_index_product.customer_group_id',
                'category_product_index.category_id'
           ))->where('category_product_index.is_parent = 0');

        if ($productIds !== null) {
            if (!is_array($productIds)) {
                $productIds = array($productIds);
            }
            $select->where('category_product_index.product_id IN(?)', $productIds);
            $selectCategory->where('category_product_index.product_id IN(?)', $productIds);
            $selectAnchorCategory->where('permission_index_product.product_id IN(?)', $productIds);
            $condition = $this->_getReadAdapter()->quoteInto('product_id IN(?)', $productIds);
        } else {
            $condition = '';
        }

        $this->_getReadAdapter()->delete($this->getTable('permission_index_product'), $condition);
        $this->_getWriteAdapter()->query($select->insertFromSelect($this->getTable('permission_index_product')));
        $this->_getWriteAdapter()->query($selectCategory->insertFromSelect($this->getTable('permission_index_product')));
        $this->_getWriteAdapter()->query($selectAnchorCategory->insertFromSelect($this->getTable('permission_index_product')));

        return $this;
    }
    /**
     * Inherit category permission from it's parent
     *
     * @param string $path
     * @return Enterprise_CatalogPermissions_Model_Mysql4_Permission_Indexer
     */
    protected function _inheritCategoryPermission($path)
    {
        if (strpos($path, '/') !== false) {
            $parentPath = substr($path, 0, strrpos($path, '/'));
        } else {
            $parentPath = '';
        }

        if (isset($this->_permissionCache[$path])) {
            foreach (array_keys($this->_permissionCache[$path]) as $uniqKey) {
                if (isset($this->_permissionCache[$parentPath][$uniqKey])) {
                    foreach ($this->_grantsInheritance as $grant => $inheritance) {
                        if ($this->_permissionCache[$path][$uniqKey][$grant] == 0) {
                            $this->_permissionCache[$path][$uniqKey][$grant] = $this->_permissionCache[$parentPath][$uniqKey][$grant];
                        } else {
                            $value = $this->_permissionCache[$parentPath][$uniqKey][$grant];

                            if ($inheritance == 'allow') {
                                $value = max(
                                    $this->_permissionCache[$path][$uniqKey][$grant],
                                    $value
                                );
                            }

                            $value = min(
                                $this->_permissionCache[$path][$uniqKey][$grant],
                                $value
                            );

                            $this->_permissionCache[$path][$uniqKey][$grant] = $value;
                        }

                        if ($this->_permissionCache[$path][$uniqKey][$grant] == 0) {
                            $this->_permissionCache[$path][$uniqKey][$grant] = null;
                        }

                    }
                }
            }
            if (isset($this->_permissionCache[$parentPath])) {
                foreach (array_keys($this->_permissionCache[$parentPath]) as $uniqKey) {
                    if (!isset($this->_permissionCache[$path][$uniqKey])) {
                        $this->_permissionCache[$path][$uniqKey] = $this->_permissionCache[$parentPath][$uniqKey];
                    }
                }
            }
        } elseif (isset($this->_permissionCache[$parentPath])) {
            $this->_permissionCache[$path] = $this->_permissionCache[$parentPath];
        }



        return $this;
    }


    /**
     * Retrive permission index for category or categories with specified customer group and website id
     *
     * @param int|array $categoryId
     * @param int $customerGroupId
     * @param int $websiteId
     * @return array
     */
    public function getIndexForCategory($categoryId, $customerGroupId, $websiteId)
    {
        if (!is_array($categoryId)) {
            $categoryId = array($categoryId);
        }

        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('category_id IN(?)', $categoryId)
            ->where('customer_group_id = ?', $customerGroupId)
            ->where('website_id = ?', $websiteId);

        return $this->_getReadAdapter()->fetchAssoc($select);
    }


    /**
     * Add index to product count select in product collection
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     * @return Enterprise_CatalogPermissions_Model_Mysql4_Permission_Index
     */
    public function addIndexToProductCount($collection, $customerGroupId)
    {
        $parts = $collection->getSelect()->getPart(Zend_Db_Select::FROM);

        if (isset($parts['permission_index_product'])) {
            return $this;
        }

        $collection->getProductCountSelect()
            ->joinLeft(
                array('permission_index_product_count'=>$this->getTable('permission_index_product')),
                'permission_index_product_count.category_id = count_table.category_id AND
                 permission_index_product_count.product_id = count_table.product_id AND
                 permission_index_product_count.store_id = count_table.store_id AND
                 permission_index_product_count.customer_group_id = ' . (int) $customerGroupId,
                array()
            );

        if (!Mage::helper('enterprise_catalogpermissions')->isAllowedCategoryView()) {
            $collection->getProductCountSelect()
                ->where('permission_index_product_count.grant_catalog_category_view = -1');
        } else {
            $collection->getProductCountSelect()
                ->where('permission_index_product_count.grant_catalog_category_view IS NULL
                         OR permission_index_product_count.grant_catalog_category_view = -1
                         OR permission_index_product_count.grant_catalog_category_view = 0');
        }

        return $this;
    }

    /**
     * Add index select in product collection
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     * @return Enterprise_CatalogPermissions_Model_Mysql4_Permission_Index
     */
    public function addIndexToProductCollection($collection, $customerGroupId)
    {
        $parts = $collection->getSelect()->getPart(Zend_Db_Select::FROM);

        if (isset($parts['permission_index_product'])) {
            return $this;
        }

        if (isset($parts['cat_index'])) {
            $condition = 'permission_index_product.category_id = cat_index.category_id AND
                 permission_index_product.product_id = cat_index.product_id AND
                 permission_index_product.store_id = cat_index.store_id AND
                 permission_index_product.customer_group_id = ' . (int) $customerGroupId;
        } else {
            $condition = 'permission_index_product.category_id IS NULL AND
                 permission_index_product.product_id = e.entity_id AND
                 permission_index_product.store_id = '. (int) $collection->getStoreId() .' AND
                 permission_index_product.customer_group_id = ' . (int) $customerGroupId;
        }

        $collection->getSelect()
            ->joinLeft(
                array('permission_index_product'=>$this->getTable('permission_index_product')),
                $condition,
                array(
                    'grant_catalog_category_view',
                    'grant_catalog_product_price',
                    'grant_checkout_items'
                )
            );

        if (!Mage::helper('enterprise_catalogpermissions')->isAllowedCategoryView()) {
            $collection->getSelect()
                ->where('permission_index_product.grant_catalog_category_view = -1');
        } else {
            $collection->getSelect()
                ->where('permission_index_product.grant_catalog_category_view IS NULL
                         OR permission_index_product.grant_catalog_category_view = -1
                         OR permission_index_product.grant_catalog_category_view = 0');
        }
        return $this;
    }

    /**
     * Add permission index to product model
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $customerGroupId
     * @return Enterprise_CatalogPermissions_Model_Mysql4_Permission_Index
     */
    public function addIndexToProduct($product, $customerGroupId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('permission_index_product'),
                array(
                    'grant_catalog_category_view',
                    'grant_catalog_product_price',
                    'grant_checkout_items'
                )
            )
            ->where('product_id = ?', $product->getId())
            ->where('customer_group_id = ?', $customerGroupId)
            ->where('store_id = ?', $product->getStoreId());

        if ($product->getCategory()) {
            $select->where('category_id = ?', $product->getCategory()->getId());
        } else {
            $select->where('category_id IS NULL');
        }

        $permission = $this->_getReadAdapter()->fetchRow($select);

        if ($permission) {
            $product->addData($permission);
        }

        return $this;
    }

    /**
     * Prepare base information for data insert
     *
     * @param   string $table
     * @param   array $fields
     * @return  Enterprise_CatalogPermissions_Model_Mysql4_Permission_Indexer
     */
    protected function _beginInsert($table, $fields)
    {
        $this->_tableFields[$table] = $fields;
        return $this;
    }

    /**
     * Put data into table
     *
     * @param   string $table
     * @param   bool $forced
     * @return  Enterprise_CatalogPermissions_Model_Mysql4_Permission_Indexer
     */
    protected function _commitInsert($table, $forced = true){
        if (isset($this->_insertData[$table]) && count($this->_insertData[$table]) && ($forced || count($this->_insertData[$table]) >= 100)) {
            $query = 'INSERT INTO ' . $this->getTable($table) . ' (' . implode(', ', $this->_tableFields[$table]) . ') VALUES ';
            $separator = '';
            foreach ($this->_insertData[$table] as $row) {
                $rowString = $this->_getWriteAdapter()->quoteInto('(?)', $row);
                $query .= $separator . $rowString;
                $separator = ', ';
            }
            $this->_getWriteAdapter()->query($query);
            $this->_insertData[$table] = array();
        }
        return $this;
    }

    /**
     * Insert data to table
     *
     * @param   string $table
     * @param   array $data
     * @return  Enterprise_CatalogPermissions_Model_Mysql4_Permission_Indexer
     */
    protected function _insert($table, $data) {
        $this->_insertData[$table][] = $data;
        $this->_commitInsert($table, false);
        return $this;
    }
}