<?php
/**
 * Entry point to the dispatch event functionality for the cases in which the queueing is needed
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PubSub
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_PubSub_Message_DispatcherAsync implements Magento_PubSub_Message_DispatcherAsyncInterface
{
    /** @var Magento_PubSub_Event_FactoryInterface */
    protected $_eventFactory;

    /** @var Magento_PubSub_Event_QueueWriterInterface  */
    protected $_eventQueue;

    /**
     * @param Magento_PubSub_Event_FactoryInterface $eventFactory
     * @param Magento_PubSub_Event_QueueWriterInterface $eventQueue
     */
    public function __construct(
        Magento_PubSub_Event_FactoryInterface $eventFactory,
        Magento_PubSub_Event_QueueWriterInterface $eventQueue
    ) {
        $this->_eventFactory = $eventFactory;
        $this->_eventQueue = $eventQueue;
    }

    /**
     * Dispatch event with given topic and data
     *
     * @param string $topic
     * @param array $data should only contain primitives, no objects.
     */
    public function dispatch($topic, $data)
    {
        $event = $this->_eventFactory->create($topic, $data);
        $this->_eventQueue->offer($event);
    }
}