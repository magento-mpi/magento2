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
class Mage_Catalog_Model_Entity_Category_Tree extends Varien_Data_Tree_Db
{
    protected $_categoryCollection;
    
    public function __construct() 
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct(
            $resource->getConnection('catalog_read'),
            $resource->getTableName('catalog/category_tree'),
            array(
                Varien_Data_Tree_Db::ID_FIELD       => 'category_id',
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
    public function load($parentNode=null, $recursionLevel=0)
    {
        parent::load($parentNode, $recursionLevel);
        $this->_loadCollection();
        return $this;
    }
    
    /**
     * Load model items for tree nodes
     *
     * @return Mage_Catalog_Model_Entity_Category_Tree
     */
    protected function _loadCollection()
    {
        $nodeIds = array();
        foreach ($this->getNodes() as $node) {
        	$nodeIds[] = $node->getId();
        }
        if (!empty($nodeIds)) {
            $collection = $this->getCategoryCollection()
                ->addAttributeToFilter('entity_id', array('in'=>$nodeIds))
                ->load();
            foreach ($collection as $item) {
            	$this->getNodeById($item->getId())->addData($item->getData());
            }
        }
        return $this;
    }
}
