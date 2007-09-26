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
        if (!$this->_cache) {
            $this->_cache = Zend_Cache::factory('Core', 'File', array(), array(
                'cache_dir'=>Mage::getBaseDir('cache_config')
            ));
        }
        return $this->_cache;
    }

    public function getResource()
    {
        if (!$this->_resource) {
            $this->_resource = Mage::getResourceModel('eav/config');
        }
        return $this->_resource;
    }

    public function getEntityType($id)
    {
        if (!is_numeric($id) && !is_string($id)) {
            throw Mage::exception('Mage_Eav', __('Invalid entity_type specified: %s', $id));
        }
        if (!isset($this->_data[$id])) {
            $data = false;
            if ($serialized = $this->getCache()->load('EAV_'.$id)) {
                if (!Mage::useCache('eav')) {
                    $this->getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('eav'));
                } else {
                    $data = unserialize($serialized);
                }
            }
            if (!$data) {
                $data = $this->getResource()->fetchEntityTypeData($id);
                if (Mage::useCache('eav')) {
                    $serialized = serialize($data);
                    $this->getCache()->save($serialized, 'EAV_'.$data['entity_type']['entity_type_id'], array('eav'));
                    $this->getCache()->save($serialized, 'EAV_'.$data['entity_type']['entity_type_code'], array('eav'));
                }
            }
            $this->_data[$data['entity_type']['entity_type_id']] = $data;
            $this->_data[$data['entity_type']['entity_type_code']] = $data['entity_type']['entity_type_id'];
        }
        if (is_string($id) && !is_numeric($id) && isset($this->_data[$id])) {
            $id = intval($this->_data[$id]);
        }
        $obj = Mage::getModel('eav/entity_type');
        if ($data = $this->_data[$id]) {
            $obj->setData($this->_data[$id]['entity_type']);
        }
        return $obj;
    }

    public function getAttribute($entityType, $id)
    {
        if (is_string($entityType) && !is_numeric($entityType)) {
            if (isset($this->_data[$entityType])) {
                $entityType = intval($this->_data[$entityType]);
            } else {
                throw Mage::exception('Mage_Eav', __('Invalid entity_type specified: %', $entityType));
            }
        }
        if (is_string($id) && !is_numeric($id)) {
            if (isset($this->_data[$entityType]['attribute'][$id])) {
                $id = intval($this->_data[$entityType]['attribute'][$id]);
            } else {
                $id = 0;
            }
        }
        $obj = Mage::getModel('eav/entity_attribute');
        if (isset($this->_data[$entityType]['attribute'][$id]) && ($data = $this->_data[$entityType]['attribute'][$id])) {
            $obj->setData($data);
        }
        return  $obj;
    }
}