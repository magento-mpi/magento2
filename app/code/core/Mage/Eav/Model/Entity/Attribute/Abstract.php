<?php

/**
 * Entity/Attribute/Model - attribute abstract
 *
 * @package    Mage
 * @subpackage Mage_Eav
 * @author     Moshe Gurvich moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
abstract class Mage_Eav_Model_Entity_Attribute_Abstract implements Mage_Eav_Model_Entity_Attribute_Interface
{
    /**
     * Attribute configuration
     *
     * @var Mage_Core_Model_Config_Element
     */
    protected $_config;
    
    /**
     * Attribute name
     *
     * @var string
     */
    protected $_name;
    
    /**
     * Attribute id
     *
     * @var string
     */
    protected $_id;
    
    
    /**
     * Entity instance
     *
     * @var Mage_Eav_Model_Entity_Abstract
     */
    protected $_entity;
    
    /**
     * Backend instance
     *
     * @var Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    protected $_backend;
    
    /**
     * Frontend instance
     *
     * @var Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
     */
    protected $_frontend;
    
    /**
     * Source instance
     *
     * @var Mage_Eav_Model_Entity_Attribute_Source_Abstract
     */
    protected $_source;
    
    /**
     * Set attribute configuration
     *
     * @param Mage_Core_Model_Config_Element $config
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function setConfig(Mage_Core_Model_Config_Element $config)
    {
        $this->_config = $config;
        $this->_name = $config->getName();
        $this->_id = (int)$config->id;
        return $this;
    }
    
    /**
     * Retrieve attribute configuration
     *
     * @return Mage_Core_Model_Config_Element
     */
    public function getConfig()
    {
        if (empty($this->_config)) {
            throw Mage::exception('Mage_Eav', 'Attribute is not initialized');
        }
        return $this->_config;
    }
    
    /**
     * Get attribute name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * Set attribute name
     *
     * @param   string $name
     * @return  Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }
    
    /**
     * Get attribute id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->_id;
    }
    
    /**
     * Set attribute entity instance
     *
     * @param Mage_Eav_Model_Entity_Abstract $entity
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function setEntity($entity)
    {
        $this->_entity = $entity;
        return $this;
    }
    
    /**
     * Retrieve entity instance
     *
     * @return Mage_Eav_Model_Entity_Abstract
     */
    public function getEntity()
    {
        return $this->_entity;
    }
    
    /**
     * Retrieve backend instance
     *
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function getBackend()
    {
        if (empty($this->_backend)) {
            $config = $this->getConfig()->backend;
            if (empty($config)) {
                $config = new Mage_Core_Model_Config_Element('<backend/>');
            }
            if (empty($config->model)) {
                $config->addChild('model', $this->_getDefaultBackendModel());
            }
            $this->_backend = Mage::getModel((string)$config->model)
                ->setConfig($config)
                ->setAttribute($this);
        }
        return $this->_backend;       
    }
    
    /**
     * Retrieve frontend instance
     *
     * @return Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
     */
    public function getFrontend()
    {
        if (empty($this->_frontend)) {
            $config = $this->getConfig()->frontend;
            if (empty($config)) {
                $config = new Mage_Core_Model_Config_Element('<frontend/>');
            }
            if (empty($config->model)) {
                $config->addChild('model', $this->_getDefaultFrontendModel());
            }
            $this->_frontend = Mage::getModel((string)$config->model)
                ->setConfig($config)
                ->setAttribute($this);
        }
        return $this->_frontend;  
    }

    /**
     * Retrieve source instance
     *
     * @return Mage_Eav_Model_Entity_Attribute_Source_Abstract
     */
    public function getSource()
    {
        if (empty($this->_source)) {
            $config = $this->getConfig()->source;
            if (empty($config)) {
                $config = new Mage_Core_Model_Config_Element('<source/>');
            }
            if (empty($config->model)) {
                $config->addChild('model', $this->_getDefaultSourceModel());
            }
            $this->_source = Mage::getModel((string)$config->model)
                ->setConfig($config)->setAttribute($this);
        }
        return $this->_source;  
    }
    
    protected function _getDefaultBackendModel()
    {
        return Mage_Eav_Model_Entity::DEFAULT_BACKEND_MODEL;
    }
    
    protected function _getDefaultFrontendModel()
    {
        return Mage_Eav_Model_Entity::DEFAULT_FRONTEND_MODEL;
    }

    protected function _getDefaultSourceModel()
    {
        return Mage_Eav_Model_Entity::DEFAULT_SOURCE_MODEL;
    }
}