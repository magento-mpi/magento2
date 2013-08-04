<?php
/**
 * Fulfills event queueing functionality for Magento, writes events to database based queue
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Event_QueueWriter implements Magento_PubSub_Event_QueueWriterInterface
{

    /** @var Mage_Webhook_Model_Event_Factory */
    protected $_eventFactory;

    /**
     * Initialize queue writer
     */
    public function __construct(Mage_Webhook_Model_Event_Factory $eventFactory)
    {
        $this->_eventFactory = $eventFactory;
    }

    /**
     * Adds event to the queue.
     *
     * @param Magento_PubSub_EventInterface $event
     * @return null
     */
    public function offer(Magento_PubSub_EventInterface $event)
    {
        if ($event instanceof Mage_Webhook_Model_Event) {
            $event->save();
        } else {
            $magentoEvent = $this->_eventFactory->create($event->getTopic(), $event->getBodyData());
            $magentoEvent->save();
        }
    }
}