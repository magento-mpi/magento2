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
namespace Magento\Webhook\Model\Resource\Event;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
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
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Core\Model\Resource\Db\Abstract $resource
     * @param null $timeoutIdling
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null,
        $timeoutIdling = null
    ) {
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
        $this->_timeoutIdling = is_null($timeoutIdling) ?
            self::DEFAULT_TIMEOUT_IDLING_EVENTS : $timeoutIdling;
    }

    public function _construct()
    {
        parent::_construct();
        $this->_init('Magento\Webhook\Model\Event', 'Magento\Webhook\Model\Resource\Event');
    }

    /**
     * Adds FOR UPDATE lock on retrieved rows and filter status
     *
     * @return \Magento\Webhook\Model\Resource\Event\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->forUpdate(true);
        $this->addFieldToFilter('status', \Magento\PubSub\EventInterface::STATUS_READY_TO_SEND)
            ->setOrder('created_at', \Magento\Data\Collection::SORT_ORDER_ASC)
            ->setPageSize(self::PAGE_SIZE);
        return $this;
    }

    /**
     * Start transaction before executing the query in order to update the status atomically
     *
     * @return \Magento\Webhook\Model\Resource\Event\Collection
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
     * @return \Magento\Webhook\Model\Resource\Event\Collection
     * @throws \Exception
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        try {
            $loadedIds = $this->_getLoadedIds();
            if (!empty($loadedIds)) {
                $this->getConnection()->update($this->getMainTable(),
                    array('status' => \Magento\PubSub\EventInterface::STATUS_IN_PROGRESS),
                    array('event_id IN (?)' => $loadedIds));
            }
            $this->getConnection()->commit();
        } catch (\Exception $e) {
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
     * @throws \Exception
     * @return null
     */
    public function revokeIdlingInProgress()
    {
        $this->getConnection()->beginTransaction();
        try {
            /* if event is in progress state for less than hour we do nothing with it*/
            $okUpdatedTime = time() - $this->_timeoutIdling;
            $this->addFieldToFilter('status', \Magento\PubSub\EventInterface::STATUS_IN_PROGRESS)
                ->addFieldToFilter('updated_at', array('to' => \Magento\Date::formatDate($okUpdatedTime),
                    'datetime' => true));
            $idsToRevoke = $this->_getLoadedIds();
            if (count($idsToRevoke)) {
                $this->getConnection()->update($this->getMainTable(),
                    array('status' => \Magento\PubSub\EventInterface::STATUS_READY_TO_SEND),
                    array('event_id IN (?)' => $idsToRevoke));
            }
        } catch (\Exception $e) {
            $this->getConnection()->rollBack();
            $this->clear();
            throw $e;
        }
        $this->getConnection()->commit();
    }
}
