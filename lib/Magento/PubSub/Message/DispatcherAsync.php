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
namespace Magento\PubSub\Message;

class DispatcherAsync implements \Magento\PubSub\Message\DispatcherAsyncInterface
{
    /** @var \Magento\PubSub\Event\FactoryInterface */
    protected $_eventFactory;

    /** @var \Magento\PubSub\Event\QueueWriterInterface  */
    protected $_eventQueue;

    /**
     * @param \Magento\PubSub\Event\FactoryInterface $eventFactory
     * @param \Magento\PubSub\Event\QueueWriterInterface $eventQueue
     */
    public function __construct(
        \Magento\PubSub\Event\FactoryInterface $eventFactory,
        \Magento\PubSub\Event\QueueWriterInterface $eventQueue
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
