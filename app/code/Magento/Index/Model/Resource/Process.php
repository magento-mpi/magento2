<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\Model\Resource;

use Magento\Index\Model\Process as ModelProcess;

/**
 * Index Process Resource Model
 *
 * @category    Magento
 * @package     Magento_Index
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Process extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Stdlib\DateTime $dateTime
     */
    public function __construct(\Magento\App\Resource $resource, \Magento\Stdlib\DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
        parent::__construct($resource);
    }

    /**
     * Initialize  table and table pk
     *
     * @return void
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
     * @return $this
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
     * @param ModelProcess $process
     * @return $this
     */
    public function endProcess(ModelProcess $process)
    {
        $data = array(
            'status'    => ModelProcess::STATUS_PENDING,
            'ended_at'  => $this->dateTime->formatDate(time()),
        );
        $this->_updateProcessData($process->getId(), $data);
        return $this;
    }

    /**
     * Register process start
     *
     * @param ModelProcess $process
     * @return $this
     */
    public function startProcess(ModelProcess $process)
    {
        $data = array(
            'status'        => ModelProcess::STATUS_RUNNING,
            'started_at'    => $this->dateTime->formatDate(time()),
        );
        $this->_updateProcessData($process->getId(), $data);
        return $this;
    }

    /**
     * Register process fail
     *
     * @param ModelProcess $process
     * @return $this
     */
    public function failProcess(ModelProcess $process)
    {
        $data = array(
            'status'   => ModelProcess::STATUS_REQUIRE_REINDEX,
            'ended_at' => $this->dateTime->formatDate(time()),
        );
        $this->_updateProcessData($process->getId(), $data);
        return $this;
    }

    /**
     * Update process status field
     *
     *
     * @param ModelProcess $process
     * @param string $status
     * @return $this
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
     * @return $this
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
     * @param ModelProcess $process
     * @return $this
     */
    public function updateProcessStartDate(ModelProcess $process)
    {
        $this->_updateProcessData($process->getId(), array('started_at' => $this->dateTime->formatDate(time())));
        return $this;
    }

    /**
     * Update process end date
     *
     * @param ModelProcess $process
     * @return $this
     */
    public function updateProcessEndDate(ModelProcess $process)
    {
        $this->_updateProcessData($process->getId(), array('ended_at' => $this->dateTime->formatDate(time())));
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
