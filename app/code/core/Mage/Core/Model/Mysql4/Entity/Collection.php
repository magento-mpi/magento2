<?php
/**
 * Entity collection resource model
 *
 * @package     Mage
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Model_Mysql4_Entity_Collection extends Varien_Data_Collection_Db
{
    protected $_entity;
    protected $_joinedAttributes = array();
    
    public function __construct() 
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('core_read'));
    }
    
    public function setEntityObject(Mage_Core_Model_Entity $entity)
    {
        if ($this->_entity) {
            Mage::throwException('You can not redeclare collection entity');
        }
        $this->_entity = $entity;
        $this->_sqlSelect->from($entity->getValueTableName());
        return $this;
    }
    
    /**
     * Retrieve entity object
     *
     * @return Mage_Core_Model_Entity
     */
    public function getEntityObject()
    {
        if ($this->_entity) {
            return $this->_entity;
        }
        Mage::throwException('Not defined entity object');
    }
    
    public function addAttributeSelect($attribute)
    {
        $this->_joinAttribute($attribute);
        return $this;
    }
    
    public function addAttributeFilter($attribute, $value, $type='and')
    {
        $attributeObject = $this->_joinAttribute($attribute);
        $this->addFilter($attributeObject->getJoinColumns(), $value, $type);
        return $this;
    }
    
    protected function _joinAttribute($attribute)
    {
        $attributeObject = $this->getEntityObject()->getAttributeCollection()
            ->getItemByColumnValue('attribute_code', $attribute);
        
        if (!$attributeObject) {
            Mage::throwException('Can not retrieve attribute by code "'.$attribute.'"');
        }
        
        if (!isset($this->_joinedAttributes[$attribute])) {
            $this->_sqlSelect->join(
                $attributeObject->getJoinTableName(),
                $attributeObject->getJoinCondition($this->getEntityObject()),
                $attributeObject->getJoinColumns(true)
            );
            $this->_joinedAttributes[$attribute] = $attributeObject;
        }
        return $this->_joinedAttributes[$attribute];
    }
}
