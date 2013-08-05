<?php
/**
 * Fulfills event queueing functionality for Magento,
 * wrapper around Magento collection with Event QueueReader Interface.
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Event_QueueReader implements Magento_PubSub_Event_QueueReaderInterface
{
    /** @var Mage_Webhook_Model_Resource_Event_Collection */
    protected $_collection;

    /** @var ArrayIterator */
    protected $_iterator;

    /**
     * Initialize collection representing the queue
     *
     * @param Mage_Webhook_Model_Resource_Event_Collection $collection
     */
    public function __construct(Mage_Webhook_Model_Resource_Event_Collection $collection)
    {
        $this->_collection = $collection;
        $this->_collection->addFieldToFilter('status', Magento_PubSub_EventInterface::READY_TO_SEND);
        $this->_iterator = $this->_collection->getIterator();
    }

    /**
     * Get the top event from the queue.
     *
     * @return Magento_PubSub_EventInterface|null
     */
    public function poll()
    {
        if ($this->_iterator->valid()) {
            /** @var Mage_Webhook_Model_Event $event */
            $event = $this->_iterator->current();

            // effectively remove it from the queue
            $event->markAsProcessed();
            $event->save();

            $this->_iterator->next();
            return $event;
        }
        return null;
    }
}