<?php

/**
 * Entity/Attribute/Model - attribute abstract
 *
 * @package    Mage
 * @subpackage Mage_Eav
 * @author     Moshe Gurvich moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
abstract class Mage_Eav_Model_Entity_Attribute_Abstract extends Mage_Core_Model_Abstract implements Mage_Eav_Model_Entity_Attribute_Interface
{
    /**
     * Attribute name
     *
     * @var string
     */
    protected $_name;

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

    protected function _construct()
    {
        $this->setResourceModel('eav/entity_attribute');
        $this->setIdFieldName('attribute_id');
        parent::_construct();
    }

    public function loadByName($entityType, $name)
    {
        if (is_numeric($entityType)) {
            $entityTypeId = $entityType;
        } elseif (is_string($entityType)) {
            $entityType = Mage::getModel('eav/entity_type')->loadByName($entityType);
        }
        if ($entityType instanceof Mage_Eav_Model_Entity_Type) {
            $entityTypeId = $entityType->getId();
        }
        if (empty($entityTypeId)) {
            throw Mage::exception('Mage_Eav', 'Invalid entity supplied');
        }
        $this->getResource()->loadByName($this, $entityTypeId, $name);
        return $this;
    }

    /**
     * Retrieve attribute configuration (deprecated)
     *
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getConfig()
    {
        return $this;
    }

    /**
     * Get attribute name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData('attribute_name');
    }

    /**
     * Get attribute alias as "entity_type/attribute_name"
     *
     * @param Mage_Eav_Model_Entity_Abstract $entity exclude this entity
     * @return string
     */
    public function getAlias($entity=null)
    {
        $alias = '';
        if (is_null($entity) || ($entity->getType() !== $this->getEntity()->getType())) {
            $alias .= $this->getEntity()->getType() . '/';
        }
        $alias .= $this->getName();
        return  $alias;
    }

    /**
     * Set attribute name
     *
     * @param   string $name
     * @return  Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function setName($name)
    {
        return $this->setData('attribute_name', $name);
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

    public function getEntityIdField()
    {
        return $this->getEntity()->getValueEntityIdField();
    }

    /**
     * Retrieve backend instance
     *
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function getBackend()
    {
        if (empty($this->_backend)) {
            if (!$this->getBackendModel()) {
                $this->setBackendModel($this->_getDefaultBackendModel());
            }
            $this->_backend = Mage::getModel($this->getBackendModel())
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
            if (!$this->getFrontendModel()) {
                $this->setFrontendModel($this->_getDefaultFrontendModel());
            }
            $this->_frontend = Mage::getModel($this->getFrontendModel())
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
            if (!$this->getSourceModel()) {
                $this->setSourceModel($this->_getDefaultSourceModel());
            }
            $this->_source = Mage::getModel($this->getSourceModel())
                ->setAttribute($this);
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