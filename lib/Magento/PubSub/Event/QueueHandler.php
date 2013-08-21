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
class Magento_PubSub_Event_QueueHandler
{
    /**
     * @var Magento_PubSub_Event_QueueReaderInterface
     */
    protected $_eventQueue;

    /**
     * @var Magento_PubSub_Job_QueueWriterInterface
     */
    protected $_jobQueue;

    /**
     * @var Magento_PubSub_Job_FactoryInterface
     */
    protected $_jobFactory;

    /**
     * @var Magento_PubSub_Subscription_CollectionInterface
     */
    protected $_subscriptionSet;

    /**
     * Initialize the class
     *
     * @param Magento_PubSub_Event_QueueReaderInterface $eventQueue
     * @param Magento_PubSub_Job_QueueWriterInterface $jobQueue
     * @param Magento_PubSub_Job_FactoryInterface $jobFactory
     * @param Magento_PubSub_Subscription_CollectionInterface $subscriptionSet
     */
    public function __construct(Magento_PubSub_Event_QueueReaderInterface $eventQueue,
        Magento_PubSub_Job_QueueWriterInterface $jobQueue,
        Magento_PubSub_Job_FactoryInterface $jobFactory,
        Magento_PubSub_Subscription_CollectionInterface $subscriptionSet
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
                /** @var $job Magento_PubSub_JobInterface */
                $job = $this->_jobFactory->create($subscription, $event);
                $this->_jobQueue->offer($job);
            }
            $event->complete();
            $event = $this->_eventQueue->poll();
        }
    }
}
