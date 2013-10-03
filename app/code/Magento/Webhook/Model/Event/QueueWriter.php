<?php
/**
 * Fulfills event queueing functionality for Magento, writes events to database based queue
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Event;

class QueueWriter implements \Magento\PubSub\Event\QueueWriterInterface
{

    /** @var \Magento\Webhook\Model\Event\Factory */
    protected $_eventFactory;

    /**
     * Initialize queue writer
     */
    public function __construct(\Magento\Webhook\Model\Event\Factory $eventFactory)
    {
        $this->_eventFactory = $eventFactory;
    }

    /**
     * Adds event to the queue.
     *
     * @param \Magento\PubSub\EventInterface $event
     * @return null
     */
    public function offer(\Magento\PubSub\EventInterface $event)
    {
        if ($event instanceof \Magento\Webhook\Model\Event) {
            $event->save();
        } else {
            $magentoEvent = $this->_eventFactory->create($event->getTopic(), $event->getBodyData());
            $magentoEvent->save();
        }
    }
}
