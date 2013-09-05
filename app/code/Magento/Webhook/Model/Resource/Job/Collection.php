<?php
/**
 * Job collection resource
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Resource_Job_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Number of jobs to load at once;
     */
    const PAGE_SIZE = 100;

    /**
     * Default time to wait for job handler to process events
     */
    const DEFAULT_TIMEOUT_IDLING_JOBS = 7200;

    /** @var int timeout to wait until decide that event is failed */
    protected $_timeoutIdling;

    /**
     * Collection constructor
     *
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     * @param int $timeoutIdling
     */
    public function __construct(
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_Resource_Db_Abstract $resource = null,
        $timeoutIdling = null
    ) {
        parent::__construct($fetchStrategy, $resource);
        $this->_timeoutIdling = is_null($timeoutIdling) ?
            self::DEFAULT_TIMEOUT_IDLING_JOBS : $timeoutIdling;
    }

    /**
     * Initialize Collection
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('Magento_Webhook_Model_Job', 'Magento_Webhook_Model_Resource_Job');
    }

    /**
     * Adds FOR UPDATE lock on retrieved rows and filter status
     *
     * @return Magento_Webhook_Model_Resource_Job_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->forUpdate(true);

        $this->addFieldToFilter('status', array(
                'in' => array(
                    \Magento\PubSub\JobInterface::STATUS_READY_TO_SEND,
                    \Magento\PubSub\JobInterface::STATUS_RETRY
                )))
            ->addFieldToFilter('retry_at', array('to' => \Magento\Date::formatDate(true), 'datetime' => true))
            ->setOrder('updated_at', \Magento\Data\Collection::SORT_ORDER_ASC)
            ->setPageSize(self::PAGE_SIZE);
        return $this;
    }

    /**
     * Start transaction before executing the query in order to update the status atomically
     *
     * @return Magento_Webhook_Model_Resource_Job_Collection
     */
    protected function _beforeLoad()
    {
        parent::_beforeLoad();
        $this->getConnection()->beginTransaction();
        return $this;
    }

    /**
     * Update the status and commit transaction in case of success
     *
     * @return Magento_Webhook_Model_Resource_Job_Collection
     * @throws Exception
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        try {
            $loadedIds = $this->_getLoadedIds();
            if (!empty($loadedIds)) {
                $this->getConnection()->update($this->getMainTable(),
                    array('status' => \Magento\PubSub\JobInterface::STATUS_IN_PROGRESS),
                    array('dispatch_job_id IN (?)' => $loadedIds));
            }
            $this->getConnection()->commit();
        } catch (Exception $e) {
            $this->getConnection()->rollBack();
            $this->clear();
            throw $e;
        }

        return $this;
    }


    /**
     * Retrieve ids of all loaded records
     *
     * @return array
     */
    protected function _getLoadedIds()
    {
        $result = array();
        foreach ($this->getItems() as $item) {
            $result[] = $item->getId();
        }
        return $result;
    }

    /**
     * Change job status back to STATUS_READY_TO_SEND if stays in STATUS_IN_PROGRESS longer than defined delay
     *
     * Regularly run by scheduling mechanism
     *
     * @throws Exception
     * @return null
     */
    public function revokeIdlingInProgress()
    {
        $this->getConnection()->beginTransaction();
        try {
            /* if event is in progress state for less than defined delay we do nothing with it */
            $okUpdatedTime = time() - $this->_timeoutIdling;
            $this->addFieldToFilter('status', \Magento\PubSub\JobInterface::STATUS_IN_PROGRESS)
                ->addFieldToFilter('updated_at', array('to' => \Magento\Date::formatDate($okUpdatedTime),
                    'datetime' => true));

            if (!count($this->getItems())) {
                $this->getConnection()->commit();
                return;
            }

            /** @var Magento_Webhook_Model_Job $job */
            foreach ($this->getItems() as $job) {
                $job->handleFailure()
                    ->save();
            }

        } catch (Exception $e) {
            $this->getConnection()->rollBack();
            $this->clear();
            throw $e;
        }
        $this->getConnection()->commit();
    }
}
