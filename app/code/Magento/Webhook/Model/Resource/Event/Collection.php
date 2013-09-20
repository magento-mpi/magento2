<?php
/**
 * Event resource Collection
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Resource_Event_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Number of events to load at once;
     */
    const PAGE_SIZE = 100;

    /**
     * Default time to wait for event handler to process events
     */
    const DEFAULT_TIMEOUT_IDLING_EVENTS = 7200;

    /** @var int timeout to wait until decide that event is failed */
    protected $_timeoutIdling;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     * @param null $timeoutIdling
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Core_Model_Resource_Db_Abstract $resource = null,
        $timeoutIdling = null
    ) {
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
        $this->_timeoutIdling = is_null($timeoutIdling) ?
            self::DEFAULT_TIMEOUT_IDLING_EVENTS : $timeoutIdling;
    }

    public function _construct()
    {
        parent::_construct();
        $this->_init('Magento_Webhook_Model_Event', 'Magento_Webhook_Model_Resource_Event');
    }

    /**
     * Adds FOR UPDATE lock on retrieved rows and filter status
     *
     * @return Magento_Webhook_Model_Resource_Event_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->forUpdate(true);
        $this->addFieldToFilter('status', Magento_PubSub_EventInterface::STATUS_READY_TO_SEND)
            ->setOrder('created_at', Magento_Data_Collection::SORT_ORDER_ASC)
            ->setPageSize(self::PAGE_SIZE);
        return $this;
    }

    /**
     * Start transaction before executing the query in order to update the status atomically
     *
     * @return Magento_Webhook_Model_Resource_Event_Collection
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
     * @return Magento_Webhook_Model_Resource_Event_Collection
     * @throws Exception
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        try {
            $loadedIds = $this->_getLoadedIds();
            if (!empty($loadedIds)) {
                $this->getConnection()->update($this->getMainTable(),
                    array('status' => Magento_PubSub_EventInterface::STATUS_IN_PROGRESS),
                    array('event_id IN (?)' => $loadedIds));
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
     * Change event status back to STATUS_READY_TO_SEND if stays in STATUS_IN_PROGRESS longer than defined delay
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
            /* if event is in progress state for less than hour we do nothing with it*/
            $okUpdatedTime = time() - $this->_timeoutIdling;
            $this->addFieldToFilter('status', Magento_PubSub_EventInterface::STATUS_IN_PROGRESS)
                ->addFieldToFilter('updated_at', array('to' => Magento_Date::formatDate($okUpdatedTime),
                    'datetime' => true));
            $idsToRevoke = $this->_getLoadedIds();
            if (count($idsToRevoke)) {
                $this->getConnection()->update($this->getMainTable(),
                    array('status' => Magento_PubSub_EventInterface::STATUS_READY_TO_SEND),
                    array('event_id IN (?)' => $idsToRevoke));
            }
        } catch (Exception $e) {
            $this->getConnection()->rollBack();
            $this->clear();
            throw $e;
        }
        $this->getConnection()->commit();
    }
}
