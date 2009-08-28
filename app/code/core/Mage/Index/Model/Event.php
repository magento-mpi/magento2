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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Index
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Index_Model_Event extends Mage_Core_Model_Abstract
{
    /**
     * Predefined event types
     */
    const TYPE_SAVE        = 'save';
    const TYPE_DELETE      = 'delete';
    const TYPE_MASS_ACTION = 'mass_action';
    const TYPE_REINDEX     = 'reindex';

    protected $_processIds = null;

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('index/event');
    }

    /**
     * Add process id to event object
     *
     * @param   $processId
     * @return  Mage_Index_Model_Event
     */
    public function addProcessId($processId)
    {
        $this->_processIds[$processId] = Mage_Index_Model_Process::EVENT_STATUS_NEW;
        return $this;
    }

    /**
     * Get event process ids
     *
     * @return array
     */
    public function getProcessIds()
    {
        return $this->_processIds;
    }

    /**
     * Merge previous event data to object.
     * Used for events duplicated protection
     *
     * @param array $data
     * @return Mage_Index_Model_Event
     */
    public function mergePreviousData($data)
    {
        if (!empty($data['event_id'])) {
            $this->setId($data['event_id']);
            $this->setCreatedAt($data['created_at']);
        }
        if (!empty($data['old_data'])) {
            $this->setOldData($data['old_data']);
        }
        if (!empty($data['new_data'])) {
            $previousNewData = unserialize($data['new_data']);
            $currentNewData  = $this->getNewData();
            $currentNewData  = array_merge($previousNewData, $currentNewData);
            $this->setNewData(serialize($currentNewData));
        }
        return $this;
    }

    /**
     * Get event old data array
     *
     * @return array
     */
    public function getOldData()
    {
        $data = $this->_getData('old_data');
        if (is_string($data)) {
            $data = unserialize($data);
        } elseif (empty($data) || !is_array($data)) {
            $data = array();
        }
        return $data;
    }

    /**
     * Get event new data array
     *
     * @return array
     */
    public function getNewData()
    {
        $data = $this->_getData('new_data');
        if (is_string($data)) {
            $data = unserialize($data);
        } elseif (empty($data) || !is_array($data)) {
            $data = array();
        }
        return $data;
    }

    /**
     * Add new values to old data array (overwrite if value with same key exist)
     *
     * @param array | string $data
     * @param null | mixed $value
     * @return Mage_Index_Model_Event
     */
    public function addOldData($key, $value=null)
    {
        $oldData = $this->getOldData();
        if (is_array($key)) {
            $oldData = array_merge_recursive($oldData, $key);
        } else {
            $oldData[$key] = $value;
        }
        $this->setOldData($oldData);
        return $this;
    }

    /**
     * Add new values to new data array (overwrite if value with same key exist)
     *
     * @param array | string $data
     * @param null | mixed $value
     * @return Mage_Index_Model_Event
     */
    public function addNewData($key, $value=null)
    {
        $newData = $this->getNewData();
        if (is_array($key)) {
            $newData = array_merge($newData, $key);
        } else {
            $newData[$key] = $value;
        }
        $this->setNewData($newData);
        return $this;
    }

    /**
     * Get event entity code.
     * Entity code declare what kind of data object related with event (product, category etc.)
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->_getData('entity');
    }

    /**
     * Get event action type.
     * Data related on self::TYPE_* constants
     *
     * @return string
     */
    public function getType()
    {
        return $this->_getData('type');
    }

    /**
     * Serelaize old and new data arrays before saving
     *
     * @return Mage_Index_Model_Event
     */
    protected function _beforeSave()
    {
        $oldData = $this->getOldData();
        $newData = $this->getNewData();
        $this->setOldData(serialize($oldData));
        $this->setNewData(serialize($newData));
        if (!$this->hasCreatedAt()) {
            $this->setCreatedAt($this->_getResource()->formatDate(time(), true));
        }
        return parent::_beforeSave();
    }
}