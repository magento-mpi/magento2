<?php
/**
 * Entity attributes collection mysql4 resource model
 *
 * @package     Mage
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Model_Mysql4_Entity_Attribute_Collection extends Varien_Data_Collection_Db
{
    protected $_attributeTable;
    
    public function __construct($attributeTable=null)
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct($resource->getConnection('core_read'));
        
        if ($attributeTable) {
            $this->setAttributeTable($attributeTable);
        }
        else {
            $this->setAttributeTable($resource->getTableName('core/attribute'));
        }
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('core/entity_attribute'));
    }
    
    public function addEntityTypeFilter($typeId)
    {
        $this->addFilter('entity_type_id', $typeId);
        return $this;
    }
    
    public function addStoreFilter()
    {
        return $this;
    }
    
    public function setPositionOrder($dir='ASC')
    {
        $this->setOrder('position', $dir);
        return $this;
    }
    
    public function setAttributeTable($attributeTable)
    {
        $this->_sqlSelect->reset(Zend_Db_Select::FROM);
        $this->_attributeTable = $attributeTable;
        $this->_sqlSelect->from($this->_attributeTable);        
        return $this;
    }
}
