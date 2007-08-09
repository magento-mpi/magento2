<?php

/**
 * Abstract model class
 *
 *
 * @copyright   2007 Varien Inc.
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @package     Mage
 * @subpackage  Core
 * @author      Moshe Gurvich <moshe@varien.com>
 */
abstract class Mage_Core_Model_Abstract extends Varien_Object
{
    /**
     * Name of the resource model
     *
     * @var string
     */
    protected $_resourceName;

    /**
     * Resource model instance
     *
     * @var Mage_Core_Model_Mysql4_Abstract
     */
    protected $_resource;

    /**
     * Name of the resource collection model
     *
     * @var string
     */
    protected $_resourceCollectionName;

    /**
     * Collection instance
     *
     * @var object
     */
    protected $_resourceCollection;

    /**
     * Standard model initialization
     *
     * @param string $resourceModel
     * @param string $idFieldName
     * @return Mage_Core_Model_Abstract
     */
    protected function _init($resourceModel)
    {
        $this->setResourceModel($resourceModel);
    }

    public function getIdFieldName()
    {
        if (!parent::getIdFieldName()) {
            $this->setIdFieldName($this->getResource()->getIdFieldName());
        }
        return parent::getIdFieldName();
    }
    
    public function getId()
    {
        if ($this->getIdFieldName()) {
            return $this->getData($this->getIdFieldName());
        } else {
            return $this->getData('id');
        }
    }
    
    public function setId($id)
    {
        if ($this->getIdFieldName()) {
            $this->setData($this->getIdFieldName(), $id);
        } else {
            $this->setData('id', $id);
        }
        return $this;
    }

    /**
     * Set resource names
     *
     * If collection name is ommited, resource name will be used with _collection appended
     *
     * @param string $resourceName
     * @param string|null $resourceCollectionName
     */
    public function setResourceModel($resourceName, $resourceCollectionName=null)
    {
        $this->_resourceName = $resourceName;
        if (is_null($resourceCollectionName)) {
            $resourceCollectionName = $resourceName.'_collection';
        }
        $this->_resourceCollectionName = $resourceCollectionName;
    }

    /**
     * Get resource instance
     *
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    public function getResource()
    {
        if (empty($this->_resource)) {
            if (empty($this->_resourceName)) {
                throw Mage::exception('Mage_Core', 'Resource is not set');
            }
            $this->_resource = Mage::getResourceSingleton($this->_resourceName);
        }
        return $this->_resource;
    }

    /**
     * Get collection instance
     *
     * @return object
     */
    public function getResourceCollection()
    {
        if (empty($this->_resourceCollection)) {
            if (empty($this->_resourceCollectionName)) {
                throw Mage::exception('Mage_Core', 'Resource is not set');
            }
            $this->_resourceCollection = Mage::getResourceModel(
                $this->_resourceCollectionName,
                $this->getResource()
            );
        }
        return $this->_resourceCollection;
    }

    /**
     * Load object data
     *
     * @param integer $id
     * @return Mage_Core_Model_Abstract
     */
    public function load($id, $field=null)
    {
        $this->getResource()->load($this, $id, $field);
        $this->_afterLoad();
        return $this;
    }

    /**
     * Save object data
     *
     * @return Mage_Core_Model_Abstract
     */
    public function save()
    {
        $this->_beforeSave();
        $this->getResource()->save($this);
        $this->_afterSave();
        return $this;
    }

    /**
     * Delete object from database
     *
     * @return Mage_Core_Model_Abstract
     */
    public function delete()
    {
        $this->_beforeDelete();
        $this->getResource()->delete($this);
        $this->_afterDelete();
        return $this;
    }

    /**
     * Retrieve data for object saving
     *
     * @return array
     */
    public function getDataForSave()
    {
        $data = $this->getData();
        return $data;
    }
    
    protected function _afterLoad()
    {
        return $this;
    }

    protected function _beforeSave()
    {
        return $this;
    }

    protected function _afterSave()
    {
        return $this;
    }

    protected function _beforeDelete()
    {
        return $this;
    }

    protected function _afterDelete()
    {
        return $this;
    }
}