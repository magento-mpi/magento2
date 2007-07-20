<?php
/**
 * Catalog category model
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Entity_Category extends Mage_Eav_Model_Entity_Abstract
{
    public function __construct() 
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('catalog_category')
            ->setConnection(
                $resource->getConnection('catalog_read'),
                $resource->getConnection('catalog_write')
            );        
    }
    
    protected function _afterDelete(Varien_Object $object){
        parent::_afterDelete($object);
        $tree = Mage::getResourceModel('catalog/category_tree')->getTree();
        $node = $tree->loadNode($object->getId());
        $tree->removeNode($node);
        return $this;
    }
    
    protected function _beforeSave(Varien_Object $object)
    {
        parent::_beforeSave($object);
        $tree = Mage::getResourceModel('catalog/category_tree')->getTree();
        $parentNode = $tree->loadNode($object->getParentId());
        if ($object->getId()) {
            
        }
        else {
            $node = $tree->appendChild(array(), $parentNode);
            $object->setId($node->getId());
        }
        return $this;
    }
}
