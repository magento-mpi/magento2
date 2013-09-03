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

class QueueHandler
{
    /**
     * @var \Magento\PubSub\Event\QueueReaderInterface
     */
    protected $_eventQueue;

    /**
     * @var \Magento\PubSub\Job\QueueWriterInterface
     */
    protected $_jobQueue;

    /**
     * @var \Magento\PubSub\Job\FactoryInterface
     */
    protected $_jobFactory;

    /**
     * @var \Magento\PubSub\Subscription\CollectionInterface
     */
    protected $_subscriptionSet;

    /**
     * Initialize the class
     *
     * @param \Magento\PubSub\Event\QueueReaderInterface $eventQueue
     * @param \Magento\PubSub\Job\QueueWriterInterface $jobQueue
     * @param \Magento\PubSub\Job\FactoryInterface $jobFactory
     * @param \Magento\PubSub\Subscription\CollectionInterface $subscriptionSet
     */
    public function __construct(\Magento\PubSub\Event\QueueReaderInterface $eventQueue,
        \Magento\PubSub\Job\QueueWriterInterface $jobQueue,
        \Magento\PubSub\Job\FactoryInterface $jobFactory,
        \Magento\PubSub\Subscription\CollectionInterface $subscriptionSet
    ) {
        $this->_eventQueue = $eventQueue;
        $this->_jobQueue = $jobQueue;
        $this->_jobFactory = $jobFactory;
        $this->_subscriptionSet = $subscriptionSet;
    }

    /**
     * Build job queue from event queue
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
