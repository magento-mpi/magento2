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
 * Index Event Resource Model
 *
 * @category    Magento
 * @package     Magento_Index
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Index_Model_Resource_Event extends Magento_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('index_event', 'event_id');
    }

    /**
     * Check if semilar event exist before start saving data
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Index_Model_Resource_Event
     */
    protected function _beforeSave(Magento_Core_Model_Abstract $object)
    {
        /**
         * Check if event already exist and merge previous data
         */
        if (!$object->getId()) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable())
                ->where('type=?', $object->getType())
                ->where('entity=?', $object->getEntity());
            if ($object->hasEntityPk()) {
                $select->where('entity_pk=?', $object->getEntityPk());
            }
            $data = $this->_getWriteAdapter()->fetchRow($select);
            if ($data) {
                $object->mergePreviousData($data);
            }
        }
        $object->cleanNewData();
        return parent::_beforeSave($object);
    }

    /**
     * Save assigned processes
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Index_Model_Resource_Event
     */
    protected function _afterSave(Magento_Core_Model_Abstract $object)
    {
        $processIds = $object->getProcessIds();
        if (is_array($processIds)) {
            $processTable = $this->getTable('index_process_event');
            if (empty($processIds)) {
                $this->_getWriteAdapter()->delete($processTable);
            } else {
                foreach ($processIds as $processId => $processStatus) {
                    if (is_null($processStatus) || $processStatus == Magento_Index_Model_Process::EVENT_STATUS_DONE) {
                        $this->_getWriteAdapter()->delete($processTable, array(
                            'process_id = ?' => $processId,
                            'event_id = ?'   => $object->getId(),
                        ));
                        continue;
                    }
                    $data = array(
                        'process_id' => $processId,
                        'event_id'   => $object->getId(),
                        'status'     => $processStatus
                    );
                    $this->_getWriteAdapter()->insertOnDuplicate($processTable, $data, array('status'));
                }
            }
        }
        return parent::_afterSave($object);
    }

    /**
     * Update status for events of process
     *
     * @param int|array|Magento_Index_Model_Process $process
     * @param string $status
     * @return Magento_Index_Model_Resource_Event
     */
    public function updateProcessEvents($process, $status = Magento_Index_Model_Process::EVENT_STATUS_DONE)
    {
        $whereCondition = '';
        if ($process instanceof Magento_Index_Model_Process) {
            $whereCondition = array('process_id = ?' => $process->getId());
        } elseif (is_array($process) && !empty($process)) {
            $whereCondition = array('process_id IN (?)' => $process);
        } elseif (!is_array($whereCondition)) {
            $whereCondition = array('process_id = ?' => $process);
        }
        $this->_getWriteAdapter()->update(
            $this->getTable('index_process_event'),
            array('status' => $status),
            $whereCondition
        );
        return $this;
    }
}
