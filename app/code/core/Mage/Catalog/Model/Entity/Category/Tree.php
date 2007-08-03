<?php
/**
 * Category tree model
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Entity_Category_Tree
{
    protected $_categoryCollection;
    protected $_categoryTree;
    protected $_root;
    protected $_nodeIds = array();
    protected $_categoryProductTable;
    protected $_productStoreTable;
    
    /**
     * Read connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;
    
    public function __construct() 
    {
        $resource = Mage::getSingleton('core/resource');
        $this->_read = $resource->getConnection('catalog_read');
        $this->_categoryTree = new Varien_Data_Tree_Db(
            $this->_read,
            $resource->getTableName('catalog/category_tree'),
            array(
                Varien_Data_Tree_Db::ID_FIELD       => 'entity_id',
                Varien_Data_Tree_Db::PARENT_FIELD   => 'pid',
                Varien_Data_Tree_Db::LEVEL_FIELD    => 'level',
                Varien_Data_Tree_Db::ORDER_FIELD    => 'order'
            )
        );
        $this->_categoryProductTable= $resource->getTableName('catalog/category_product');
        $this->_productStoreTable   = $resource->getTableName('catalog/product_store');
    }
    
    /**
     * Retrieve category collection object
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getCategoryCollection()
    {
        if (!$this->_categoryCollection) {
            $this->_categoryCollection = Mage::getResourceModel('catalog/category_collection');
        }
        return $this->_categoryCollection;
    }
    
    /**
     * Load tree
     *
     * @param int $parentNode
     * @param int $recursionLevel
     * @return Mage_Catalog_Model_Entity_Category_Tree
     */
    public function load($parentNode, $recursionLevel=100)
    {
        $this->_root = $this->getTree()->loadNode($parentNode)
            ->loadChildren($recursionLevel);
        $this->_loadCollection();
        return $this;
    }
    
    
    public function loadProductCount($storeId = null)
    {
        $categoryIds = $this->_getNodeIds();
        if (is_null($storeId)) {
            $storeId = Mage::getSingleton('core/store')->getId();
        }
        
        if (!empty($categoryIds)) {
            $select = $this->_read->select();
            $select->from($this->_categoryProductTable, 
                    array('category_id',
                    new Zend_Db_Expr('count('.$this->_productStoreTable.'.product_id)')))
                ->join($this->_productStoreTable, 
                    $this->_productStoreTable.'.product_id='.$this->_categoryProductTable.'.product_id')
                ->where($this->_read->quoteInto('category_id IN (?)', $categoryIds))
                ->where($this->_read->quoteInto('store_id=?', $storeId))
                ->group($this->_categoryProductTable.'.category_id');
            
            $counts = $this->_read->fetchPairs($select);
            
            foreach ($counts as $categoryId=>$productCount) {
            	$this->getTree()->getNodeById($categoryId)->setProductCount($productCount);
            }
        }
        return $this;
    }
    
    public function getTree()
    {
        return $this->_categoryTree;
    }
    
    public function getRoot()
    {
        return $this->_root;
    }
    
    protected function _getNodeIds()
    {
        if (empty($this->_nodeIds)) {
            foreach ($this->getTree()->getNodes() as $node) {
        	   $this->_nodeIds[] = $node->getId();
            }
        }
        return $this->_nodeIds;
    }
    
    /**
     * Load model items for tree nodes
     *
     * @return Mage_Catalog_Model_Entity_Category_Tree
     */
    protected function _loadCollection()
    {
        $nodeIds = $this->_getNodeIds();
        if (!empty($nodeIds)) {
            $collection = $this->getCategoryCollection()
                ->addAttributeToFilter('entity_id', array('in'=>$nodeIds))
                ->load();
            foreach ($collection as $item) {
            	$this->getTree()->getNodeById($item->getId())->addData($item->getData());
            }
        }
        return $this;
    }
}
