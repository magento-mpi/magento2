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
namespace Magento\Index\Model\Resource\Process;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Index\Model\Process', 'Magento\Index\Model\Resource\Process');
    }

    /**
     * Add count of unprocessed events to process collection
     *
     * @return \Magento\Index\Model\Resource\Process\Collection
     */
    public function addEventsStats()
    {
        $countsSelect = $this->getConnection()
            ->select()
            ->from($this->getTable('index_process_event'), array('process_id', 'events' => 'COUNT(*)'))
            ->where('status=?', \Magento\Index\Model\Process::EVENT_STATUS_NEW)
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
