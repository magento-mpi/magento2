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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Category flat model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_loaded = false;

    protected $_nodes = array();

    protected function  _construct()
    {
        $this->_init('catalog/category_flat', 'entity_id');
    }

    /**
     * Load nodes by parent id
     *
     * @param integer $parentId
     * @param integer $recursionLevel
     * @param integer $storeId
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat
     */
    protected function _loadNodes($parentNode = null, $recursionLevel = 0, $storeId = 0)
    {
        $_conn = $this->_getReadAdapter();
        $startLevel = 1;
        $parentPath = '';
        if ($parentNode instanceof Mage_Catalog_Model_Category) {
            $parentPath = $parentNode->getPath();
            $startLevel = $parentNode->getLevel();
        } elseif (is_numeric($parentNode)) {
            $selectParent = $_conn->select()
                ->from($this->getMainTable())
                ->where('entity_id = ?', $parentNode)
                ->where('store_id = ?', '0');
            if ($parentNode = $_conn->fetchRow($selectParent)) {
                $parentPath = $parentNode['path'];
                $startLevel = $parentNode['level'];
            }
        }
        $select = $_conn->select()
            ->from(array('main_table'=>$this->getMainTable()))
            ->joinLeft(
                array('url_rewrite'=>$this->getTable('core/url_rewrite')),
                'url_rewrite.category_id=main_table.entity_id AND url_rewrite.is_system=1 AND url_rewrite.product_id IS NULL AND url_rewrite.store_id="'.$storeId.'" AND url_rewrite.id_path LIKE "category/%"',
                array('request_path' => 'url_rewrite.request_path'))
            ->where('main_table.store_id = ?', $storeId)
            ->where('main_table.is_active = ?', '1')
            ->order('main_table.position ASC');

        if ($parentPath) {
            $select->where($_conn->quoteInto("main_table.path like ?", "$parentPath/%"));
        }
        if ($recursionLevel != 0) {
            $select->where("main_table.level <= ?", $startLevel + $recursionLevel);
        }
//        Zend_Debug::dump($select->__toString());
        $arrNodes = $_conn->fetchAll($select);
        $nodes = array();
        foreach ($arrNodes as $node) {
            $node['id'] = $node['entity_id'];
            $nodes[$node['id']] = Mage::getModel('catalog/category')->setData($node);
        }
        return $nodes;
    }

    /**
     * Load nodes by parent id
     *
     * @param integer $parentId
     * @param integer $recursionLevel
     * @param integer $storeId
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat
     */
    public function loadNodes($parentId, $recursionLevel = 0, $storeId = 0)
    {
//        Varien_Profiler::start('CATALOG_FLAT: '.__METHOD__);
        if (!$this->_loaded) {
            $_conn = $this->_getReadAdapter();
            $startLevel = 1;
            $parentPath = '';
            $selectParent = $_conn->select()
                ->from($this->getMainTable())
                ->where('entity_id = ?', $parentId)
                ->where('store_id = ?', '0');
            $parentNode = $_conn->fetchRow($selectParent);
            if ($parentNode) {
                $parentPath = $parentNode['path'];
                $startLevel = $parentNode['level'];
            }
            $this->_nodes[$parentNode['entity_id']] = new Varien_Object($parentNode);
            $select = $_conn->select()
                ->from(array('main_table'=>$this->getMainTable()))
                ->joinLeft(
                    array('url_rewrite'=>$this->getTable('core/url_rewrite')),
                    'url_rewrite.category_id=main_table.entity_id AND url_rewrite.is_system=1 AND url_rewrite.product_id IS NULL AND url_rewrite.store_id="'.$storeId.'" AND url_rewrite.id_path LIKE "category/%"',
                    array('request_path' => 'url_rewrite.request_path'))
                ->where('main_table.store_id = ?', $storeId)
                ->where('main_table.is_active = ?', '1')
                ->order('main_table.position ASC');

            if ($parentPath) {
                $select->where($_conn->quoteInto("main_table.path like ?", "$parentPath/%"));
            }
            if ($recursionLevel != 0) {
                $select->where("main_table.level <= ?", $startLevel + $recursionLevel);
            }
//Zend_Debug::dump($select->__toString());die();
            $arrNodes = $_conn->fetchAll($select);

            $childrenItems = array();

            foreach ($arrNodes as $nodeInfo) {
                $pathToParent = explode('/', $nodeInfo['path']);
                array_pop($pathToParent);
                $pathToParent = implode('/', $pathToParent);
                $nodeInfo['id'] = $nodeInfo['entity_id'];
                $category = Mage::getModel('catalog/category')->setData($nodeInfo);
                $childrenItems[$pathToParent][] = $category;
            }
            $this->addChildNodes($childrenItems, $parentPath, &$this->_nodes[$parentNode['entity_id']]);
            $childrenNodes = $this->_nodes[$parentNode['entity_id']];
            if ($childrenNodes->getChildrenNodes()) {
                $this->_nodes = $childrenNodes->getChildrenNodes();
            }
            $this->_loaded = true;
        }
//        Varien_Profiler::stop('CATALOG_FLAT: '.__METHOD__);
        return $this;
    }

    /**
     * Creating sorted array of nodes
     *
     * @param array $children
     * @param string $path
     * @param Varien_Object $parent
     */
    public function addChildNodes($children, $path, $parent)
    {
        if (isset($children[$path])) {
            foreach ($children[$path] as $child) {
                $childrenNodes = $parent->getChildrenNodes();
                if ($childrenNodes && isset($childrenNodes[$child->getId()])) {
                    $childrenNodes[$child['entity_id']]->setChildrenNodes(array($child->getId()=>$child));
                } else {
                    if ($childrenNodes) {
                        $childrenNodes[$child->getId()] = $child;
                    } else {
                        $childrenNodes = array($child->getId()=>$child);
                    }
                    $parent->setChildrenNodes($childrenNodes);
                }

                if ($path) {
                    $childrenPath = explode('/', $path);
                } else {
                    $childrenPath = array();
                }
                $childrenPath[] = $child->getId();
                $childrenPath = implode('/', $childrenPath);
                $this->addChildNodes($children, $childrenPath, $child);
            }
        }
    }

    /**
     * Return sorted array of nodes
     *
     * @return array
     */
    public function getNodes($parentId = null, $recursionLevel = 0, $storeId = null)
    {
        if (!$this->_loaded) {
            $selectParent = $this->_getReadAdapter()->select()
                ->from($this->getMainTable())
                ->where('entity_id = ?', $parentId)
                ->where('store_id = ?', '0');
            if ($parentNode = $this->_getReadAdapter()->fetchRow($selectParent)) {
                $parentNode['id'] = $parentNode['entity_id'];
                $parentNode = Mage::getModel('catalog/category')->setData($parentNode);
                $this->_nodes[$parentNode->getId()] = $parentNode;
                $nodes = $this->_loadNodes($parentNode, $recursionLevel, $storeId);
                $childrenItems = array();
                foreach ($nodes as $node) {
                    $pathToParent = explode('/', $node->getPath());
                    array_pop($pathToParent);
                    $pathToParent = implode('/', $pathToParent);
                    $childrenItems[$pathToParent][] = $node;
                }
                $this->addChildNodes($childrenItems, $parentNode->getPath(), &$this->_nodes[$parentNode->getId()]);
                $childrenNodes = $this->_nodes[$parentNode->getId()];
                if ($childrenNodes->getChildrenNodes()) {
                    $this->_nodes = $childrenNodes->getChildrenNodes();
                }
                $this->_loaded = true;
            }
        }
        return $this->_nodes;
    }

    /**
     * Return node with id $nodeId
     *
     * @param integer $nodeId
     * @param array $nodes
     * @return Varien_Object
     */
    public function getNodeById($nodeId, $nodes = null)
    {
        if (is_null($nodes)) {
            $nodes = $this->getNodes();
        }
        foreach ($nodes as $node) {
            if ($node->getId() == $nodeId) {
                return $node;
            }
            if ($node->getChildrenNodes()) {
                return $this->getNodeById($nodeId, $node->getChildrenNodes());
            }
        }
        return array();
    }

    /**
     * Adding product counts to categories
     *
     * @todo 2 sql query to get all counts
     * @param array $nodes
     * @return array
     */
    public function addProductCountToCategories($nodes)
    {
        $layer = Mage::getSingleton('catalog/layer');
        $productCollection = Mage::getResourceModel('catalog/product_collection');
        $layer->prepareProductCollection($productCollection);
        foreach ($nodes as $node) {
                $select = clone $productCollection->getSelect();
                $select->reset(Zend_Db_Select::COLUMNS);
                $select->reset(Zend_Db_Select::GROUP);
                $select->reset(Zend_Db_Select::ORDER);
                $select->distinct(false);
                $select->join(
                    array('category_count_table' => $this->getTable('catalog/category_product')),
                    'category_count_table.product_id=e.entity_id',
                    array('count_in_category'=>new Zend_Db_Expr('COUNT(DISTINCT e.entity_id)'))
                );
                if ($node->getIsAnchor()) {
                    $select->where($this->_getReadAdapter()->quoteInto(
                        'category_count_table.category_id IN(?)',
                        $this->getAllChildrenIds($node)
                    ));
                } else {
                    $select->where($this->_getReadAdapter()->quoteInto(
                        'category_count_table.category_id=?',
                        $node->getId()
                    ));
                }

                $node->setProductCount((int) $this->_getReadAdapter()->fetchOne($select));
        }
        return $nodes;
    }

    public function isRebuilded()
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), new Zend_Db_Expr('COUNT(entity_id)'));
        try {
            $_isRebuileded = (bool) $this->_getReadAdapter()->fetchOne($select);
        } catch (Exception $e) {
            return false;
        }
        return $_isRebuileded;
    }

    /**
     * Rebuild flat data from eav
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat
     */
    public function rebuild()
    {
        $_read  = $this->_getReadAdapter();
        $_read->query("DROP TABLE IF EXISTS `{$this->getMainTable()}`");
        $_read->query("CREATE TABLE `{$this->getMainTable()}` (
                `entity_id` int(10) unsigned not null,
                `store_id` smallint(5) unsigned not null default '0',
                `parent_id` int(10) unsigned not null default '0',
                `path` varchar(255) not null default '',
                `level` int(11) not null default '0',
                `position` int(11) not null default '0',
                `children_count` int(11) not null,
                `created_at` datetime not null default '0000-00-00 00:00:00',
                `updated_at` datetime not null default '0000-00-00 00:00:00',
                KEY `CATEGORY_FLAT_CATEGORY_ID` (`entity_id`),
                KEY `CATEGORY_FLAT_STORE_ID` (`store_id`),
                KEY `path` (`path`),
                KEY `IDX_LEVEL` (`level`),
                CONSTRAINT `FK_CATEGORY_FLAT_CATEGORY_ID` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('catalog/category')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `FK_CATEGORY_FLAT_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Flat Category'
        ");
        $selectStore = $_read->select()
            ->from($this->getTable('core/store'));
        $resultStore = $_read->fetchAll($selectStore);
        $stores = array();
        foreach($resultStore as $store) {
            $stores[] = $store['store_id'];
        }
        $categoriesArray = array();
        $select = $_read->select()
            ->from($this->getTable('catalog/category'));
        $result = $_read->fetchAll($select);

        foreach ($result as $row) {
            foreach ($stores as $store) {
                $_read->insert(
                    $this->getMainTable(),
                    array(
                        'entity_id' => $row['entity_id'],
                        'store_id' => $store,
                        'parent_id' => $row['parent_id'],
                        'path' => $row['path'],
                        'level' => $row['level'],
                        'position' => $row['position'],
                        'children_count' => $row['children_count'],
                        'created_at' => $row['created_at'],
                        'updated_at' => $row['updated_at']
                    )
                );
            }
            $categoriesArray[$row['entity_id']] = array();
        }
        $_write = $this->_getWriteAdapter();
        $selectAttribute = $_read->select()
            ->from($this->getTable('eav/entity_type'), array())
            ->join(
                $this->getTable('eav/attribute'),
                $this->getTable('eav/attribute').'.entity_type_id = '.$this->getTable('eav/entity_type').'.entity_type_id',
                $this->getTable('eav/attribute').'.*'
            )
            ->where($this->getTable('eav/entity_type').'.entity_type_code=?', 'catalog_category');
        $resultAttribute = $_read->fetchAll($selectAttribute);
        foreach ($resultAttribute as $attribute) {
            $type = '';
            switch ($attribute['backend_type']) {
                case 'varchar':
                    $type = 'varchar(255) not null default \'\'';
                    $selectAttribute = $_read->select()
                        ->from($this->getTable('catalog/category').'_varchar')
                        ->where('attribute_id=?', $attribute['attribute_id']);
                    $resultAttribute = $_read->fetchAll($selectAttribute);
                    break;
                case 'int':
                    $type = 'int(10) not null default \'0\'';
                    $selectAttribute = $_read->select()
                        ->from($this->getTable('catalog/category').'_int')
                        ->where('attribute_id=?', $attribute['attribute_id']);
                    $resultAttribute = $_read->fetchAll($selectAttribute);
                    break;
                case 'text':
                    $type = 'text';
                    $selectAttribute = $_read->select()
                        ->from($this->getTable('catalog/category').'_text')
                        ->where('attribute_id=?', $attribute['attribute_id']);
                    $resultAttribute = $_read->fetchAll($selectAttribute);
                    break;
                case 'datetime':
                    $type = 'datetime not null default \'0000-00-00 00:00:00\'';
                    $selectAttribute = $_read->select()
                        ->from($this->getTable('catalog/category').'_datetime')
                        ->where('attribute_id=?', $attribute['attribute_id']);
                    $resultAttribute = $_read->fetchAll($selectAttribute);
                    break;
                case 'decimal':
                    $type = 'decimal(10,2) not null default \'0.00\'';
                    $selectAttribute = $_read->select()
                        ->from($this->getTable('catalog_category_entity').'_decimal')
                        ->where('attribute_id=?', $attribute['attribute_id']);
                    $resultAttribute = $_read->fetchAll($selectAttribute);
                    break;
            }
            if ($type) {
                $_write->addColumn($this->getMainTable(), $attribute['attribute_code'], $type);
                foreach ($resultAttribute as $attributeRow) {
                    $categoriesArray[$attributeRow['entity_id']][$attributeRow['store_id']][$attribute['attribute_code']] = $attributeRow['value'];
                }
            }
        }
        foreach($categoriesArray as $categoryId=>$categoryData) {
            foreach($stores as $store) {
                if (!isset($categoryData[$store])) {
                    $categoryData[$store] = array();
                }
                    $_write->update(
                        $this->getMainTable(),
                        array_merge($categoryData['0'], $categoryData[$store]),
                        $_read->quoteInto('entity_id=?', $categoryId) . ' AND ' . $_read->quoteInto('store_id=?', $store)
                    );
//                } else {
//                    $_write->delete(
//                        $this->getMainTable(),
//                        $_read->quoteInto('entity_id=?', $categoryId) . ' AND ' . $_read->quoteInto('store_id=?', $store)
//                    );
//                }
            }
        }
        return $this;
    }

    /**
     * Synchronize flat data with eav model
     *
     * @param Mage_Catalog_Model_Category $category
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat
     */
    public function synchronize($category, $moving = false, $prevParentPath = '', $parentPath = '')
    {
        // update all static fields (path, children_count, parent_id etc.) for all store views
        if ($moving) {
            $_staticFields = array(
                'parent_id',
                'path',
                'level',
                'position',
                'children_count',
                'updated_at'
            );
            $update = "UPDATE {$this->getMainTable()}, {$this->getTable('catalog/category')} SET";
            foreach ($_staticFields as $field) {
                $update .= " {$this->getMainTable()}.".$field."={$this->getTable('catalog/category')}.".$field.",";
            }
            $update = substr($update, 0, -1);
            $update .= " WHERE {$this->getMainTable()}.entity_id = {$this->getTable('catalog/category')}.entity_id AND " .
                "({$this->getTable('catalog/category')}.path like '$parentPath/%' OR " .
                "{$this->getTable('catalog/category')}.path like '$prevParentPath/%')";
            $this->_getWriteAdapter()->query($update);
        } else {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable(), 'entity_id')
                ->where('entity_id = ?', $category->getId())
                ->where('store_id = ?', $category->getStoreId());
            if ($result = $this->_getReadAdapter()->fetchOne($select)) {
                // update
                foreach ($category->getStoreIds() as $storeId) {
                    $tmpCategory = Mage::getModel('catalog/category')
                        ->setStoreId($storeId)
                        ->load($category->getId());
                    $this->_getWriteAdapter()->update(
                        $this->getMainTable(),
                        $this->_prepareDataForAllFields($tmpCategory),
                        $this->_getReadAdapter()->quoteInto('entity_id = ?', $category->getId()) .
                            ' AND ' . $this->_getReadAdapter()->quoteInto('store_id = ?', $tmpCategory->getStoreId())
                    );
                    $tmpCategory = null;
                }
            } else {
                // insert
                $origStoreId = $category->getStoreId();
                foreach ($category->getStoreIds() as $storeId) {
                    $category->setStoreId($storeId);
                    $this->_getWriteAdapter()->insert(
                        $this->getMainTable(),
                        $this->_prepareDataForAllFields($category)
                    );
                }
                $category->setStoreId($origStoreId);
            }
        }
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Catalog_Model_Category $category
     * @param array $replaceFields
     * @return array
     */
    protected function _prepareDataForAllFields($category, $replaceFields = array())
    {
        $table = $this->_getReadAdapter()->describeTable($this->getMainTable());
        $data = array();
        foreach ($table as $column=>$columnData) {
            if ($category->getData($column)) {
                if (key_exists($column, $replaceFields)) {
                    $value = $category->getData($replaceFields[$column]);
                } else {
                    $value = $category->getData($column);
                }
                if (is_array($value)) {
                    $value = implode(',', $value);
                }
                $data[$column] = $value;
            }
        }
        return $data;
    }

    /**
     * Get count of active/not active children categories
     *
     * @param   Mage_Catalog_Model_Category $category
     * @param   bool $isActiveFlag
     * @return  integer
     */
    public function getChildrenAmount($category, $isActiveFlag = true)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), "COUNT({$this->getMainTable()}.entity_id)")
            ->where("{$this->getMainTable()}.path LIKE ?", $category->getPath() . '/%')
            ->where("{$this->getMainTable()}.store_id = ?", $category->getStoreId())
            ->where("{$this->getMainTable()}.is_active = ?", (int) $isActiveFlag);
        return (int) $this->_getReadAdapter()->fetchOne($select);
    }

/**
     * Get products count in category
     *
     * @param Mage_Catalog_Model_Category $category
     * @return integer
     */
    public function getProductCount($category)
    {
        $select =  $this->_getReadAdapter()->select()
            ->from($this->getTable('catalog/category_product'), "COUNT({$this->getTable('catalog/category_product')}.product_id)")
            ->where("{$this->getTable('catalog/category_product')}.category_id = ?", $category->getId())
            ->group("{$this->getTable('catalog/category_product')}.category_id");
        return (int) $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Return parent categories of category
     *
     * @param Mage_Catalog_Model_Category $category
     * @return array
     */
    public function getParentCategories($category, $isActive = true)
    {
        $categories = array();
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('entity_id in (?)', array_reverse(explode(',', $category->getPathInStore())))
            ->where('store_id = ?', $category->getStoreId());
        if ($isActive) {
            $select->where('is_active = ?', '1');
        }
        $select->order('path', 'ASC');
        $result = $this->_getReadAdapter()->fetchAll($select);
        foreach ($result as $row) {
            $row['id'] = $row['entity_id'];
            $categories[$row['entity_id']] = Mage::getModel('catalog/category')->setData($row);
        }
        return $categories;
    }

    /**
     * Return children categories of category
     *
     * @param Mage_Catalog_Model_Category $category
     * @return array
     */
    public function getChildrenCategories($category)
    {
        $node = $this->getNodeById($category->getId());
        if ($node && $children = $node->getChildrenNodes()) {
            return $children;
        }
        $categories = $this->_loadNodes($category, 1, $category->getStoreId());
        return $categories;
    }

    /**
     * Return children ids of category
     *
     * @param Mage_Catalog_Model_Category $category
     * @param integer $level
     * @return array
     */
    public function getChildren($category, $level = 1, $isActive = true)
    {
        Varien_Profiler::start('CATALOG_FLAT_getChildren:');
//        $_categories = $this->_getNodesOrLoad($category);
//        if (is_null($_categories)) {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'entity_id')
            ->where('path LIKE ?', "{$category->getPath()}/%");
        if ($level) {
            $select->where('level <= ?', $category->getLevel() + $level);
        }
        if ($isActive) {
            $select->where('is_active = ?', '1');
        }
        $select->where('store_id = ?', $category->getStoreId());
        $_categories = $this->_getReadAdapter()->fetchAll($select);
//        }
//        if (is_null($_categories)) {
//            $_categories = $this->_loadNodes($category, $level, $category->getStoreId());
//        }
        $categoriesIds = array();
        foreach ($_categories as $_category) {
//            $categoriesIds[] = $_category->getId();
            $categoriesIds[] = $_category['entity_id'];
        }
        Varien_Profiler::stop('CATALOG_FLAT_getChildren:');
        return $categoriesIds;
    }

    /**
     * Return all children ids of category (with category id)
     *
     * @param Mage_Catalog_Model_Category $category
     * @return array
     */
    public function getAllChildren($category)
    {
        $categoriesIds = $this->getChildren($category, 0);
        $myId = array($category->getId());
        $categoriesIds = array_merge($myId, $categoriesIds);

        return $categoriesIds;
    }

    /**
     * Return nodes from loaded nodes
     *
     * @param Mage_Catalog_Model_Category $category
     * @return array | null
     */
    protected function _getNodesOrLoad($category)
    {
        $node = $this->getNodeById($category->getId());
        if ($node && $children = $node->getChildrenNodes()) {
            return $children;
        }
        return null;
    }

/**
     * Check if category id exist
     *
     * @param   int $id
     * @return  bool
     */
    public function checkId($id)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'entity_id')
            ->where('entity_id=?', $id);
        return $this->_getReadAdapter()->fetchOne($select);
    }
}