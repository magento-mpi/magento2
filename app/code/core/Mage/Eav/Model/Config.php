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
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Eav_Model_Config
{
    /**
     * Runtime cache for entity types information
     *
     * @var array
     */
    protected $_cache;

    /**
     * Array data loaded from cache
     *
     * @var array
     */
    protected $_data;
    /**
     * Runtime cache for entity types information
     *
     * @var array
     */
    protected $_objects = array();

    /**
     * Db Resource Model
     *
     * @var Mage_Eav_Model_Mysql4_Config
     */
    protected $_resource;

    /**
     * Get Eav Config Cache Object
     *
     * @return Zend_Cache_Core
     */
    public function getCache()
    {
        return Mage::app()->getCache();
    }

    public function getResource()
    {
        if (!$this->_resource) {
            $this->_resource = Mage::getResourceModel('eav/config');
        }
        return $this->_resource;
    }

    protected function _initEntityData($id)
    {
        if (!isset($this->_data[$id])) {
            $data = false;
            if ($serialized = Mage::app()->loadCache('EAV_'.$id)) {
                if (!Mage::app()->useCache('eav')) {
                    Mage::app()->cleanCache(array('eav'));
                } else {
                    $data = unserialize($serialized);
                }
            }

            if (!$data) {
                $data = $this->getResource()->fetchEntityTypeData($id);
                if (!$data) {
                    throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid entity_type specified: %', $id));
                }
                $this->saveEntityCache($data);
            }
            $this->_data[$data['entity']['entity_type_id']] = $data;
            $this->_data[$data['entity']['entity_type_code']] = $data['entity']['entity_type_id'];
        }
        return $this;
    }

    public function getEntityType($id)
    {
        if ($id instanceof Mage_Eav_Model_Entity_Type) {
            $this->_initEntityData($id->getId());
            return $id;
        }
        if (!is_numeric($id) && !is_string($id)) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid entity_type specified: %s', $id));
        }

        $this->_initEntityData($id);

        if (is_string($id) && !is_numeric($id) && isset($this->_data[$id])) {
            $id = intval($this->_data[$id]);
        }
        $obj = Mage::getModel('eav/entity_type');
        if (isset($this->_data[$id])) {
            $obj->setData($this->_data[$id]['entity']);
        }
        return $obj;
    }

    public function getAttribute($entityType, $id)
    {
        if ($id instanceof Mage_Eav_Model_Entity_Attribute_Interface) {
            return $id;
        }
        $entityType = $this->getEntityType($entityType);
        $entityTypeId = $entityType->getId();
        $attributeModel = $entityType->getAttributeModel() ? $entityType->getAttributeModel() : 'eav/entity_attribute';
        $obj = Mage::getModel($attributeModel);
        if (isset($this->_data[$entityTypeId]['attributes'][$id])) {
            if (!is_numeric($id)) {
                $id = intval($this->_data[$entityTypeId]['attributes'][$id]);
            }
            if ($id) {
                $data = $this->_data[$entityTypeId]['attributes'][$id];
                $obj->setData($data);
            }
        }
        /**
         * For initialize attribute data can use only $this->_data
         * (static attributes don't need try load from DB)
         */
        /*else {
            if (is_numeric($id)) {
                $obj->load($id);
            } else {
                $obj->loadByCode($entityType, $id);
            }
            if ($obj->getAttributeId()) {
                $this->_data[$entityTypeId]['attributes'][$obj->getAttributeId()] = $obj->getData();
                $this->_data[$entityTypeId]['attributes'][$obj->getAttributeCode()] = $obj->getAttributeId();
            } else {
                $this->_data[$entityTypeId]['attributes'][$id] = false;
            }
            $this->saveEntityCache($this->_data[$entityTypeId]);
        }*/
        return $obj;
    }

    public function getEntityAttributeCodes($entityType)
    {
        $codes = array();
        $entityType = $this->getEntityType($entityType);
        $entityTypeId = $entityType->getId();
        if (isset($this->_data[$entityTypeId]['attributes'])) {
            foreach ($this->_data[$entityTypeId]['attributes'] as $index => $info) {
            	if (is_array($info)) {
                    $codes[] = $info['attribute_code'];
            	}
            }
        }

        return $codes;
    }

    public function saveEntityCache($data)
    {
        if (Mage::app()->useCache('eav')) {
            $serialized = serialize($data);
            Mage::app()->saveCache($serialized, 'EAV_'.$data['entity']['entity_type_id'], array('eav'));
            Mage::app()->saveCache($serialized, 'EAV_'.$data['entity']['entity_type_code'], array('eav'));
        }
        return $this;
    }
}