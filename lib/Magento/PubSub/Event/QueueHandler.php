<?php
/**
 * Handles event queue, uses it to build job queue
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PubSub
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PubSub\Event;

use Magento\PubSub\Job\FactoryInterface as JobFactoryInterface;
use Magento\PubSub\Job\QueueWriterInterface as JobQueueWriterInterface;
use Magento\PubSub\Subscription\CollectionInterface;

class QueueHandler
{
    /**
     * @var QueueReaderInterface
     */
    protected $_eventQueue;

    /**
     * @var JobQueueWriterInterface
     */
    protected $_jobQueue;

    /**
     * @var JobFactoryInterface
     */
    protected $_jobFactory;

    /**
     * @var CollectionInterface
     */
    protected $_subscriptionSet;

    /**
     * Initialize the class
     *
     * @param QueueReaderInterface $eventQueue
     * @param JobQueueWriterInterface $jobQueue
     * @param JobFactoryInterface $jobFactory
     * @param CollectionInterface $subscriptionSet
     */
    public function __construct(QueueReaderInterface $eventQueue,
        JobQueueWriterInterface $jobQueue,
        JobFactoryInterface $jobFactory,
        CollectionInterface $subscriptionSet
    ) {
        $this->_eventQueue = $eventQueue;
        $this->_jobQueue = $jobQueue;
        $this->_jobFactory = $jobFactory;
        $this->_subscriptionSet = $subscriptionSet;
    }

    /**
     * Build job queue from event queue
     *
     * @return void
     */
    public function handle()
    {
        $event = $this->_eventQueue->poll();
        while (!is_null($event)) {
            $subscriptions = $this->_subscriptionSet->getSubscriptionsByTopic($event->getTopic());
            foreach ($subscriptions as $subscription) {
                /** @var $job \Magento\PubSub\JobInterface */
                $job = $this->_jobFactory->create($subscription, $event);
                $this->_jobQueue->offer($job);
            }
            $event->complete();
            $event = $this->_eventQueue->poll();
        }
    }
}
