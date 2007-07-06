<?php

/**
 * Entity/Attribute/Model - attribute backend abstract
 *
 * @package    Mage
 * @subpackage Mage_Eav
 * @author     Moshe Gurvich moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
abstract class Mage_Eav_Model_Entity_Attribute_Backend_Abstract implements Mage_Eav_Model_Entity_Attribute_Backend_Interface
{
    /**
     * Reference to the attribute instance
     *
     * @var Mage_Eav_Model_Entity_Attribute_Abstract
     */
    protected $_attribute;
    
    /**
     * PK value_id for loaded entity (for faster updates)
     *
     * @var integer
     */
    protected $_valueId;
    
    /**
     * Table name for this attribute
     *
     * @var string
     */
    protected $_table;
    
    /**
     * Name of the entity_id field for the value table of this attribute
     *
     * @var string
     */
    protected $_entityIdField;
    
    /**
     * Default value for the attribute
     *
     * @var mixed
     */
    protected $_defaultValue = null;
    
    /**
     * Set attribute instance
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function setAttribute($attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }
    
    /**
     * Get attribute instance
     *
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttribute()
    {
        return $this->_attribute;
    }
    
    /**
     * Get backend type of the attribute
     *
     * @return string
     */
    public function getType()
    {
        return $this->getAttribute()->getBackendType();
    }
    
    /**
     * Check whether the attribute is a real field in the entity table
     *
     * @return boolean
     */
    public function isStatic()
    {
        return (string)$this->getType()==='' || $this->getType()==='static';
    }
    
    /**
     * Get table name for the values of the attribute
     *
     * @return string
     */
    public function getTable()
    {
        if (empty($this->_table)) {
            if ($this->isStatic()) {
                $this->_table = false;
            } elseif ($this->getAttribute()->getBackendTable()) {
                $this->_table = $this->getAttribute()->getBackendTable();
            } else {
                $this->_table = $this->getAttribute()->getEntity()->getValueTablePrefix()
                    .'_'.$this->getType();
            }
        }
        
        return $this->_table;
    }
    
    /**
     * Get entity_id field in the attribute values tables
     *
     * @return string
     */
    public function getEntityIdField()
    {
        if (empty($this->_entityIdField)) {
            if ($this->getAttribute()->getEntityIdField()) {
                $this->_entityIdField = $this->getAttribute()->getEntityIdField();
            } else {
                $this->_entityIdField = $this->getAttribute()->getEntity()->getValueEntityIdField();
            }
        }
        return $this->_entityIdField;
    }
    
    public function setValueId($valueId)
    {
        $this->_valueId = $valueId;
        return $this;
    }
    
    public function getValueId()
    {
        return $this->_valueId;
    }
    
    public function getDefaultValue()
    {
        if (is_null($this->_defaultValue)) {
            if ($this->getAttribute()->getDefaultValue()) {
                $this->_defaultValue = $this->getAttribute()->getDefaultValue();
            } else {
                $this->_defaultValue = "";
            }
        }
        return $this->_defaultValue;
    }
    
    public function validate($object)
    {
        $attrName = $this->getAttribute()->getName();
        if ($this->getAttribute()->is('required') && !$object->getData($attrName)) {
            return false;
        }
        return true;
    }
    
    public function afterLoad($object)
    {
        
    }
    
    public function beforeSave($object)
    {
        $attrName = $this->getAttribute()->getName();
        if (!$object->hasData($attrName)) {
            $object->setData($attrName, $this->getDefaultValue());
        }
    }
    
    public function afterSave($object)
    {
        
    }
    
    public function beforeDelete($object)
    {
        
    }
    
    public function afterDelete($object)
    {
        
    }
}