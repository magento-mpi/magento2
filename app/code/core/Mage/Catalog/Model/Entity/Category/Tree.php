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
    public function __construct() 
    {
        $resource = Mage::getSingleton('core/resource');
        
        parent::__construct(
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
    
    public function addCollectionData($collection)
    {
        $nodeIds = array();
        foreach ($this->getNodes() as $node) {
        	$nodeIds[] = $node->getId();
        }
        
        $collection->addIdFilter($nodeIds)
            ->load();
        foreach ($collection as $category) {
        	$this->getNodeById($category->getId())->addData($category->getData());
        }
        return $this;
    }
}
