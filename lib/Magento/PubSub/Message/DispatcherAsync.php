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

use Magento\PubSub\Event\FactoryInterface;
use Magento\PubSub\Event\QueueWriterInterface;

class DispatcherAsync implements DispatcherAsyncInterface
{
    /** @var FactoryInterface */
    protected $_eventFactory;

    /** @var QueueWriterInterface  */
    protected $_eventQueue;

    /**
     * @param FactoryInterface $eventFactory
     * @param QueueWriterInterface $eventQueue
     */
    public function __construct(
        FactoryInterface $eventFactory,
        QueueWriterInterface $eventQueue
    ) {
        $this->_eventFactory = $eventFactory;
        $this->_eventQueue = $eventQueue;
    }

    /**
     * Dispatch event with given topic and data
     *
     * @param string $topic
     * @param array $data should only contain primitives, no objects.
     * @return void
     */
    public function dispatch($topic, $data)
    {
        $event = $this->_eventFactory->create($topic, $data);
        $this->_eventQueue->offer($event);
    }
}
