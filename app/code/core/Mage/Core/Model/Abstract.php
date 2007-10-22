<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Abstract model class
 *
 *
 * @category   Mage
 * @package    Mage_Core
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
    * Original data that was loaded from resource
    * 
    * @var array
    */
    protected $_origData;

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
        if (!($fieldName = parent::getIdFieldName())) {
            $fieldName = $this->getResource()->getIdFieldName();
            $this->setIdFieldName($fieldName);
        }
        return $fieldName;
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
                throw Mage::exception('Mage_Core', __('Resource is not set'));
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
                throw Mage::exception('Mage_Core', __('Resource is not set'));
            }
            $this->_resourceCollection = Mage::getResourceModel(
                $this->_resourceCollectionName,
                $this->getResource()
            );
        }
        return $this->_resourceCollection;
    }
    
    public function getOrigData($key=null)
    {
        if (is_null($key)) {
            return $this->_origData;
        }
        return isset($this->_origData[$key]) ? $this->_origData[$key] : null;
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
        $this->_origData = $this->_data;
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
