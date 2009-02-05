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

    protected $_isRebuilded = null;

    protected function  _construct()
    {
        $this->_init('catalog/category_flat', 'entity_id');
    }

    public function getMainStoreTable($storeId = 0)
    {
        $table = $this->getMainTable();
        if (is_string($storeId)) {
            $storeId = intval($storeId);
        }
        if ($this->getUseStoreTables() && $storeId) {
            $table .= '_'.Mage::app()->getStore($storeId)->getCode();
        }
        return $table;
    }

    public function getUseStoreTables()
    {
        return true;
        Mage::app()->getConfig()->getNode('/asdf');
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
                ->from($this->getMainStoreTable())
                ->where('entity_id = ?', $parentNode)
                ->where('store_id = ?', '0');
            if ($parentNode = $_conn->fetchRow($selectParent)) {
                $parentPath = $parentNode['path'];
                $startLevel = $parentNode['level'];
            }
        }
        $select = $_conn->select()
            ->from(array('main_table'=>$this->getMainStoreTable(Mage::app()->getStore()->getId())))
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
                ->from($this->getMainStoreTable())
                ->where('entity_id = ?', $parentId)
                ->where('store_id = ?', '0');
            $parentNode = $_conn->fetchRow($selectParent);
            if ($parentNode) {
                $parentPath = $parentNode['path'];
                $startLevel = $parentNode['level'];
            }
            $this->_nodes[$parentNode['entity_id']] = new Varien_Object($parentNode);
            $select = $_conn->select()
                ->from(array('main_table'=>$this->getMainStoreTable(Mage::app()->getStore()->getId())))
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
                ->from($this->getMainStoreTable())
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
     * Check if category flat data is rebuilded
     *
     * @return bool
     */
    public function isRebuilded()
    {
        if ($this->_isRebuilded === null) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable(), 'entity_id')
                ->limit(1);
            try {
                $this->_isRebuilded = (bool) $this->_getReadAdapter()->fetchOne($select);
            } catch (Exception $e) {
                $this->_isRebuilded = false;
            }
        }
        return $this->_isRebuilded;
    }

    protected function _getTableSqlSchema($storeId = 0)
    {
        $schema = "CREATE TABLE `{$this->getMainStoreTable($storeId)}` (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        return $schema;
    }

    /**
     * Rebuild flat data from eav
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat
     */
    public function rebuild()
    {
        $_read  = $this->_getReadAdapter();
        $_write = $this->_getWriteAdapter();
        $_read->query("DROP TABLE IF EXISTS `{$this->getMainStoreTable()}`");
        $_read->query($this->_getTableSqlSchema());
        $stores = array();
        if ($this->getUseStoreTables()) {
            $selectStores = $_read->select()
                ->from($this->getTable('core/store'), 'store_id');
            $stores = $_read->fetchAll($selectStores);
            foreach ($stores as $store) {
                $_read->query("DROP TABLE IF EXISTS `{$this->getMainStoreTable($store['store_id'])}`");
                $_write->query($this->_getTableSqlSchema($store['store_id']));
            }
        }
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
                    break;
                case 'int':
                    $type = 'int(10) not null default \'0\'';
                    break;
                case 'text':
                    $type = 'text';
                    break;
                case 'datetime':
                    $type = 'datetime not null default \'0000-00-00 00:00:00\'';
                    break;
                case 'decimal':
                    $type = 'decimal(10,2) not null default \'0.00\'';
                    break;
            }
            if ($type) {
                $_write->addColumn($this->getMainStoreTable(), $attribute['attribute_code'], $type);
                if ($this->getUseStoreTables()) {
                    foreach ($stores as $store) {
                        $_write->addColumn($this->getMainStoreTable($store['store_id']), $attribute['attribute_code'], $type);
                    }
                }
            }
        }
        $_categories = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('*')
            ->load();
        foreach ($_categories as $_category) {
            foreach ($_category->getStoreIds() as $_storeId) {
                $_category->setStoreId($_storeId);
                $this->_synchronize($_category, 'insert');
            }
        }
        return $this;
    }

    /**
     * Synchronize flat data with eav model.
     *
     * @param Mage_Catalog_Model_Category $category
     * @param null|string $action
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat
     */
    protected function _synchronize($category, $action = null)
    {
        if (is_null($action)) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainStoreTable($category->getStoreId()), 'entity_id')
                ->where('entity_id = ?', $category->getId())
                ->where('store_id = ?', $category->getStoreId());
            if ($result = $this->_getReadAdapter()->fetchOne($select)) {
                $action = 'update';
            } else {
                $action = 'insert';
            }
        }

        if ($action == 'update') {
            // update
            $this->_getWriteAdapter()->update(
                $this->getMainStoreTable($category->getStoreId()),
                $this->_prepareDataForAllFields($category),
                $this->_getReadAdapter()->quoteInto('entity_id = ?', $category->getId()) .
                    ' AND ' . $this->_getReadAdapter()->quoteInto('store_id = ?', $category->getStoreId())
            );
        } elseif ($action == 'insert') {
            // insert
            $this->_getWriteAdapter()->insert(
                $this->getMainStoreTable($category->getStoreId()),
                $this->_prepareDataForAllFields($category)
            );
        }
        return $this;
    }

    /**
     * Synchronize flat data with eav model when category was moved.
     *
     * @param string $prevParentPath
     * @param string $parentPath
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat
     */
    protected function _move($prevParentPath, $parentPath)
    {
        $_staticFields = array(
            'parent_id',
            'path',
            'level',
            'position',
            'children_count',
            'updated_at'
        );
        $update = "UPDATE {$this->getMainStoreTable()}, {$this->getTable('catalog/category')} SET";
        foreach ($_staticFields as $field) {
            $update .= " {$this->getMainStoreTable()}.".$field."={$this->getTable('catalog/category')}.".$field.",";
        }
        $update = substr($update, 0, -1);
        $update .= " WHERE {$this->getMainStoreTable()}.entity_id = {$this->getTable('catalog/category')}.entity_id AND " .
            "({$this->getTable('catalog/category')}.path like '$parentPath/%' OR " .
            "{$this->getTable('catalog/category')}.path like '$prevParentPath/%')";
        $this->_getWriteAdapter()->query($update);

        return $this;
    }

    /**
     * Synchronize flat data with eav model.
     *
     * @param Mage_Catalog_Model_Category $category
     * @param array $storeIds
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat
     */
    public function synchronize($category = null, $storeIds = array())
    {
        if (is_null($category)) {
            $storesCondition = '';
            if (!empty($storeIds)) {
                $storesCondition = $this->_getReadAdapter()->quoteInto(
                    ' AND s.store_id IN (?)', $storeIds
                );
            }
            $stores = $this->_getReadAdapter()->fetchAll("
                SELECT
                    s.store_id, s.website_id, c.path AS root_path
                FROM
                    {$this->getTable('core/store')} AS s,
                    {$this->getTable('core/store_group')} AS sg,
                    {$this->getTable('catalog/category')} AS c
                WHERE
                    sg.group_id=s.group_id
                    AND c.entity_id=sg.root_category_id
                    {$storesCondition}
            ");
            foreach ($stores as $store) {
                $_categories = Mage::getModel('catalog/category')->getCollection()
                    ->setStoreId($store['store_id'])
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter(array(
                        array('attribute' => 'path', 'like' => "{$store['root_path']}/%"),
                        array('attribute' => 'path', 'eq' => "{$store['root_path']}")
                    ))
                    ->load();
                $this->_getWriteAdapter()->delete(
                    $this->getMainStoreTable($store['store_id']),
                    $this->_getReadAdapter()->quoteInto('store_id = ?', $store['store_id'])
                );
                foreach ($_categories as $_category) {
                    $_category->setStoreId($store['store_id']);
                    $this->_synchronize($_category, 'insert');
                }
            }
        } elseif ($category instanceof Mage_Catalog_Model_Category) {
            foreach ($category->getStoreIds() as $storeId) {
                $_tmpCategory = Mage::getModel('catalog/category')
                    ->setStoreId($storeId)
                    ->load($category->getId());
                $_tmpCategory->setStoreId($storeId);
                $this->_synchronize($_tmpCategory);
                $_tmpCategory = null;
            }
        }
        return $this;
    }

    public function move($prevParentPath, $parentPath)
    {
        $this->_move($prevParentPath, $parentPath);
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
        $table = $this->_getReadAdapter()->describeTable($this->getMainStoreTable($category->getStoreId()));
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
            ->from($this->getMainStoreTable($category->getStoreId()), "COUNT({$this->getMainStoreTable($category->getStoreId())}.entity_id)")
            ->where("{$this->getMainStoreTable($category->getStoreId())}.path LIKE ?", $category->getPath() . '/%')
            ->where("{$this->getMainStoreTable($category->getStoreId())}.store_id = ?", $category->getStoreId())
            ->where("{$this->getMainStoreTable($category->getStoreId())}.is_active = ?", (int) $isActiveFlag);
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
            ->from($this->getMainStoreTable($category->getStoreId()))
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
            ->from($this->getMainStoreTable($category->getStoreId()), 'entity_id')
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
            ->from($this->getMainStoreTable(), 'entity_id')
            ->where('entity_id=?', $id);
        return $this->_getReadAdapter()->fetchOne($select);
    }
}