<?php

/**
 * Entity/Attribute/Model - attribute frontend abstract
 *
 * @package    Mage
 * @subpackage Mage_Eav
 * @author     Moshe Gurvich moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
abstract class Mage_Eav_Model_Entity_Attribute_Frontend_Abstract implements Mage_Eav_Model_Entity_Attribute_Frontend_Interface
{
    /**
     * Frontend configuration
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
     * Set frontend configuration
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
     * Retrieve frontend configuration
     *
     * @return Mage_Core_Model_Config_Element
     */
    public function getConfig()
    {
        if (empty($this->_config)) {
            throw Mage::exception('Mage_Eav', 'Frontend is not initialized');
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
     * Get attribute type for user interface form
     *
     * @return string
     */
    public function getInputType()
    {
        return (string)$this->getConfig()->input;
    }
    
    /**
     * Get select options in case it's select box and options source is defined
     *
     * @return array
     */
    public function getSelectOptions()
    {
        return $this->getAttribute()->getSource()->getAllOptions();
    }
}