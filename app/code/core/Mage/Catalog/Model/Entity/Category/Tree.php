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
    
    public function __construct() 
    {
        $resource = Mage::getSingleton('core/resource');
        $this->_categoryTree = new Varien_Data_Tree_Db(
            $resource->getConnection('catalog_read'),
            $resource->getTableName('catalog/category_tree'),
            array(
                Varien_Data_Tree_Db::ID_FIELD       => 'entity_id',
                Varien_Data_Tree_Db::PARENT_FIELD   => 'pid',
                Varien_Data_Tree_Db::LEVEL_FIELD    => 'level',
                Varien_Data_Tree_Db::ORDER_FIELD    => 'order'
            )
        );
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
    public function load($parentNode, $recursionLevel=0)
    {
        $this->_root = $this->getTree()->loadNode($parentNode)
            ->loadChildren($recursionLevel);
        $this->_loadCollection();
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
    
    /**
     * Load model items for tree nodes
     *
     * @return Mage_Catalog_Model_Entity_Category_Tree
     */
    protected function _loadCollection()
    {
        $nodeIds = array();
        foreach ($this->getTree()->getNodes() as $node) {
        	$nodeIds[] = $node->getId();
        }
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
