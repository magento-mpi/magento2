<?php

/**
 * Entity/Attribute/Model - attribute selection source abstract
 *
 * @package    Mage
 * @subpackage Mage_Eav
 * @author     Moshe Gurvich moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
abstract class Mage_Eav_Model_Entity_Attribute_Source_Abstract implements Mage_Eav_Model_Entity_Attribute_Source_Interface
{
    /**
     * Source configuration
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
     * Options array
     *
     * @var array
     */
    protected $_options = array();
    
    /**
     * Set source configuration
     *
     * @param Mage_Core_Model_Config_Element $config
     * @return Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
     */
    public function setConfig(Mage_Core_Model_Config_Element $config)
    {
        $this->_config = $config;
        return $this;
    }
    
    /**
     * Retrieve source configuration
     *
     * @return Mage_Core_Model_Config_Element
     */
    public function getConfig()
    {
        if (empty($this->_config)) {
            throw Mage::exception('Mage_Eav', 'Source is not initialized');
        }
        return $this->_config;
    }
    
    /**
     * Set attribute instance
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
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
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        if (isset($options[$value])) {
            return $options[$value];
        }
        return false;
    }
}