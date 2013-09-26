<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Newsletter queue resource model
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Newsletter_Model_Resource_Queue extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Subscriber collection
     *
     * @var Magento_Newsletter_Model_Resource_Subscriber_Collection
     */
    protected $_subscriberCollection;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Newsletter_Model_Resource_Subscriber_Collection $subscriberCollection
     */
    public function __construct(
        Magento_Core_Model_Resource $resource,
        Magento_Newsletter_Model_Resource_Subscriber_Collection $subscriberCollection
    ) {
        parent::__construct($resource);
        $this->_subscriberCollection = $subscriberCollection;
    }

    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('newsletter_queue', 'queue_id');
    }

    /**
     * Add subscribers to queue
     *
     * @param Magento_Newsletter_Model_Queue $queue
     * @param array $subscriberIds
     * @throws Magento_Core_Exception
     */
    public function addSubscribersToQueue(Magento_Newsletter_Model_Queue $queue, array $subscriberIds)
    {
        if (count($subscriberIds)==0) {
            throw new Magento_Core_Exception(__('There are no subscribers selected.'));
        }

        if (!$queue->getId() && $queue->getQueueStatus()!=Magento_Newsletter_Model_Queue::STATUS_NEVER) {
            throw new Magento_Core_Exception(__('You selected an invalid queue.'));
        }

        $adapter = $this->_getWriteAdapter();

        $select = $adapter->select();
        $select->from($this->getTable('newsletter_queue_link'), 'subscriber_id')
            ->where('queue_id = ?', $queue->getId())
            ->where('subscriber_id in (?)', $subscriberIds);

        $usedIds = $adapter->fetchCol($select);
        $adapter->beginTransaction();
        try {
            foreach ($subscriberIds as $subscriberId) {
                if (in_array($subscriberId, $usedIds)) {
                    continue;
                }
                $data = array();
                $data['queue_id'] = $queue->getId();
                $data['subscriber_id'] = $subscriberId;
                $adapter->insert($this->getTable('newsletter_queue_link'), $data);
            }
            $adapter->commit();
        }
        catch (Exception $e) {
            $adapter->rollBack();
        }
    }

    /**
     * Removes subscriber from queue
     *
     * @param Magento_Newsletter_Model_Queue $queue
     */
    public function removeSubscribersFromQueue(Magento_Newsletter_Model_Queue $queue)
    {
        $adapter = $this->_getWriteAdapter();
        try {
            $adapter->beginTransaction();
            $adapter->delete(
                $this->getTable('newsletter_queue_link'),
                array(
                    'queue_id = ?' => $queue->getId(),
                    'letter_sent_at IS NULL'
                )
            );

            $adapter->commit();
        }
        catch (Exception $e) {
            $adapter->rollBack();
            throw $e;
        }
    }

    /**
     * Links queue to store
     *
     * @param Magento_Newsletter_Model_Queue $queue
     * @return Magento_Newsletter_Model_Resource_Queue
     */
    public function setStores(Magento_Newsletter_Model_Queue $queue)
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->delete(
            $this->getTable('newsletter_queue_store_link'),
            array('queue_id = ?' => $queue->getId())
        );

        $stores = $queue->getStores();
        if (!is_array($stores)) {
            $stores = array();
        }

        foreach ($stores as $storeId) {
            $data = array();
            $data['store_id'] = $storeId;
            $data['queue_id'] = $queue->getId();
            $adapter->insert($this->getTable('newsletter_queue_store_link'), $data);
        }
        $this->removeSubscribersFromQueue($queue);

        if (count($stores) == 0) {
            return $this;
        }

        $subscribers = $this->_subscriberCollection->addFieldToFilter('store_id', array('in'=>$stores))
            ->useOnlySubscribed()
            ->load();

        $subscriberIds = array();

        foreach ($subscribers as $subscriber) {
            $subscriberIds[] = $subscriber->getId();
        }

        if (count($subscriberIds) > 0) {
            $this->addSubscribersToQueue($queue, $subscriberIds);
        }

        return $this;
    }

    /**
     * Returns queue linked stores
     *
     * @param Magento_Newsletter_Model_Queue $queue
     * @return array
     */
    public function getStores(Magento_Newsletter_Model_Queue $queue)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from($this->getTable('newsletter_queue_store_link'), 'store_id')
            ->where('queue_id = :queue_id');

        if (!($result = $adapter->fetchCol($select, array('queue_id'=>$queue->getId())))) {
            $result = array();
        }

        return $result;
    }

    /**
     * Saving template after saving queue action
     *
     * @param Magento_Core_Model_Abstract $queue
     * @return Magento_Newsletter_Model_Resource_Queue
     */
    protected function _afterSave(Magento_Core_Model_Abstract $queue)
    {
        if ($queue->getSaveStoresFlag()) {
            $this->setStores($queue);
        }
        return $this;
    }
}
