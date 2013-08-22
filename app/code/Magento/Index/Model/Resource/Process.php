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
 * Index Process Resource Model
 *
 * @category    Magento
 * @package     Magento_Index
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Index_Model_Resource_Process extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize  table and table pk
     *
     */
    protected function _construct()
    {
        $this->_init('index_process', 'process_id');
    }

    /**
     * Update process/event association row status
     *
     * @param int $processId
     * @param int $eventId
     * @param string $status
     * @return Magento_Index_Model_Resource_Process
     */
    public function updateEventStatus($processId, $eventId, $status)
    {
        $adapter = $this->_getWriteAdapter();
        $condition = array(
            'process_id = ?' => $processId,
            'event_id = ?'   => $eventId
        );
        $adapter->update($this->getTable('index_process_event'), array('status' => $status), $condition);
        return $this;
    }

    /**
     * Register process end
     *
     * @param Magento_Index_Model_Process $process
     * @return Magento_Index_Model_Resource_Process
     */
    public function endProcess(Magento_Index_Model_Process $process)
    {
        $data = array(
            'status'    => Magento_Index_Model_Process::STATUS_PENDING,
            'ended_at'  => $this->formatDate(time()),
        );
        $this->_updateProcessData($process->getId(), $data);
        return $this;
    }

    /**
     * Register process start
     *
     * @param Magento_Index_Model_Process $process
     * @return Magento_Index_Model_Resource_Process
     */
    public function startProcess(Magento_Index_Model_Process $process)
    {
        $data = array(
            'status'        => Magento_Index_Model_Process::STATUS_RUNNING,
            'started_at'    => $this->formatDate(time()),
        );
        $this->_updateProcessData($process->getId(), $data);
        return $this;
    }

    /**
     * Register process fail
     *
     * @param Magento_Index_Model_Process $process
     * @return Magento_Index_Model_Resource_Process
     */
    public function failProcess(Magento_Index_Model_Process $process)
    {
        $data = array(
            'status'   => Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX,
            'ended_at' => $this->formatDate(time()),
        );
        $this->_updateProcessData($process->getId(), $data);
        return $this;
    }

    /**
     * Update process status field
     *
     *
     * @param Magento_Index_Model_Process $process
     * @param string $status
     * @return Magento_Index_Model_Resource_Process
     */
    public function updateStatus($process, $status)
    {
        $data = array('status' => $status);
        $this->_updateProcessData($process->getId(), $data);
        return $this;
    }

    /**
     * Updates process data
     * @param int $processId
     * @param array $data
     * @return Magento_Index_Model_Resource_Process
     */
    protected function _updateProcessData($processId, $data)
    {
        $bind = array('process_id=?' => $processId);
        $this->_getWriteAdapter()->update($this->getMainTable(), $data, $bind);

        return $this;
    }

    /**
     * Update process start date
     *
     * @param Magento_Index_Model_Process $process
     * @return Magento_Index_Model_Resource_Process
     */
    public function updateProcessStartDate(Magento_Index_Model_Process $process)
    {
        $this->_updateProcessData($process->getId(), array('started_at' => $this->formatDate(time())));
        return $this;
    }

    /**
     * Update process end date
     *
     * @param Magento_Index_Model_Process $process
     * @return Magento_Index_Model_Resource_Process
     */
    public function updateProcessEndDate(Magento_Index_Model_Process $process)
    {
        $this->_updateProcessData($process->getId(), array('ended_at' => $this->formatDate(time())));
        return $this;
    }

    /**
     * Whether transaction is already started
     *
     * @return bool
     */
    public function isInTransaction()
    {
        return $this->_getWriteAdapter()->getTransactionLevel() > 0;
    }
}
