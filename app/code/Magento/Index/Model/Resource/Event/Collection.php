<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Index Event Collection
 *
 * @category    Magento
 * @package     Magento_Index
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Index_Model_Resource_Event_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Index_Model_Event', 'Magento_Index_Model_Resource_Event');
    }

    /**
     * Add filter by entity
     *
     * @param string | array $entity
     * @return Magento_Index_Model_Resource_Event_Collection
     */
    public function addEntityFilter($entity)
    {
        if (is_array($entity) && !empty($entity)) {
            $this->addFieldToFilter('entity', array('in'=>$entity));
        } else {
            $this->addFieldToFilter('entity', $entity);
        }
        return $this;
    }

    /**
     * Add filter by type
     *
     * @param string | array $type
     * @return Magento_Index_Model_Resource_Event_Collection
     */
    public function addTypeFilter($type)
    {
        if (is_array($type) && !empty($type)) {
            $this->addFieldToFilter('type', array('in'=>$type));
        } else {
            $this->addFieldToFilter('type', $type);
        }
        return $this;
    }

    /**
     * Add filter by process and status to events collection
     *
     * @param int|array|Magento_Index_Model_Process $process
     * @param string $status
     * @return Magento_Index_Model_Resource_Event_Collection
     */
    public function addProcessFilter($process, $status = null)
    {
        $this->_joinProcessEventTable();
        if ($process instanceof Magento_Index_Model_Process) {
            $this->addFieldToFilter('process_event.process_id', $process->getId());
        } elseif (is_array($process) && !empty($process)) {
            $this->addFieldToFilter('process_event.process_id', array('in' => $process));
        } else {
            $this->addFieldToFilter('process_event.process_id', $process);
        }

        if ($status !== null) {
            if (is_array($status) && !empty($status)) {
                $this->addFieldToFilter('process_event.status', array('in' => $status));
            } else {
                $this->addFieldToFilter('process_event.status', $status);
            }
        }
        return $this;
    }

    /**
     * Join index_process_event table to event table
     *
     * @return Magento_Index_Model_Resource_Event_Collection
     */
    protected function _joinProcessEventTable()
    {
        if (!$this->getFlag('process_event_table_joined')) {
            $this->getSelect()->join(array('process_event' => $this->getTable('index_process_event')),
                'process_event.event_id=main_table.event_id',
                array('process_event_status' => 'status')
            );
            $this->setFlag('process_event_table_joined', true);
        }
        return $this;
    }

    /**
     * Reset collection state
     *
     * @return Magento_Index_Model_Resource_Event_Collection
     */
    public function reset()
    {
        $this->_totalRecords = null;
        $this->_data = null;
        $this->_isCollectionLoaded = false;
        $this->_items = array();
        return $this;
    }
}
