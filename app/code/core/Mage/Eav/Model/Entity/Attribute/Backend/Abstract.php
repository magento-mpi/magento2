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
     * Backend configuration
     *
     * @var Mage_Core_Model_Config_Element
     */
    protected $_config;
    
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
     * Set backend configuration
     *
     * @param Mage_Core_Model_Config_Element $config
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function setConfig(Mage_Core_Model_Config_Element $config)
    {
        $this->_config = $config;
        return $this;
    }
    
    /**
     * Retrieve backend configuration
     *
     * @return Mage_Core_Model_Config_Element
     */
    public function getConfig()
    {
        if (empty($this->_config)) {
            throw Mage::exception('Mage_Eav', 'Backend is not initialized');
        }
        return $this->_config;
    }
    
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
        return (string)$this->getConfig()->type;
    }
    
    /**
     * Check whether the attribute is a real field in the entity table
     *
     * @return boolean
     */
    public function isStatic()
    {
        return !$this->getType();
    }
    
    /**
     * Get table name for the values of the attribute
     *
     * @return string
     */
    public function getTable()
    {
        if (empty($this->_table)) {
            if ($this->getConfig()->table) {
                $this->_table = (string)$this->getConfig()->table;
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
            if ($this->getConfig()->entity_id_field) {
                $this->_entityIdField = (string)$this->getConfig()->entity_id_field;
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
            if ($this->getConfig()->default_value) {
                $this->_defaultValue = (string)$this->getConfig()->default_value;
            } else {
                $this->_defaultValue = "";
            }
        }
        return $this->_defaultValue;
    }
    
    public function validate($object)
    {
        $attrName = $this->getAttribute()->getName();
        if ($this->getConfig()->is('required') && !$object->getData($attrName)) {
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