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
 * Index Process Collection
 *
 * @category    Magento
 * @package     Magento_Index
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Index_Model_Resource_Process_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Index_Model_Process', 'Magento_Index_Model_Resource_Process');
    }

    /**
     * Add count of unprocessed events to process collection
     *
     * @return Magento_Index_Model_Resource_Process_Collection
     */
    public function addEventsStats()
    {
        $countsSelect = $this->getConnection()
            ->select()
            ->from($this->getTable('index_process_event'), array('process_id', 'events' => 'COUNT(*)'))
            ->where('status=?', Magento_Index_Model_Process::EVENT_STATUS_NEW)
            ->group('process_id');
        $this->getSelect()
            ->joinLeft(
                array('e' => $countsSelect),
                'e.process_id=main_table.process_id',
                array('events' => $this->getConnection()->getCheckSql(
                    $this->getConnection()->prepareSqlCondition('e.events', array('null' => null)), 0, 'e.events'
                ))
            );
        return $this;
    }
}
