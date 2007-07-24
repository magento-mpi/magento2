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
    protected $_tree;
    public function __construct() 
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('catalog_category')
            ->setConnection(
                $resource->getConnection('catalog_read'),
                $resource->getConnection('catalog_write')
            );        
    }
    
    /**
     * Retrieve category tree object
     *
     * @return Varien_Data_Tree_Db
     */
    protected function _getTree()
    {
        if (!$this->_tree) {
            $this->_tree = Mage::getResourceModel('catalog/category_tree')->getTree();
        }
        return $this->_tree;
    }
    
    protected function _afterDelete(Varien_Object $object){
        parent::_afterDelete($object);
        $node = $this->_getTree()->loadNode($object->getId());
        $this->_getTree()->removeNode($node);
        return $this;
    }
    
    protected function _beforeSave(Varien_Object $object)
    {
        parent::_beforeSave($object);
        $parentNode = $this->_getTree()->loadNode($object->getParentId());
        if ($object->getId()) {
            
        }
        else {
            $node = $this->_getTree()->appendChild(array(), $parentNode);
            $object->setId($node->getId());
        }
        return $this;
    }
    
    protected function _afterSave(Varien_Object $object)
    {
        parent::_afterSave($object);
    }
    
    protected function _insertAttribute($object, $attribute, $value, $storeIds = array())
    {
        return parent::_insertAttribute($object, $attribute, $value);
    }

}
