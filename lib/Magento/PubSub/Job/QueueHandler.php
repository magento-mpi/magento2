<?php
/**
 * Dispatches HTTP messages derived from job queue and handles the responses
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PubSub
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_PubSub_Job_QueueHandler
{
    /**
     * @var Magento_PubSub_Job_QueueReaderInterface
     */
    protected $_jobQueue;

    /**
     * @var Magento_Outbound_TransportInterface
     */
    protected $_transport;

    /**
     * @var Magento_Outbound_Message_FactoryInterface
     */
    protected $_messageFactory;

    /**
     * @param Magento_PubSub_Job_QueueReaderInterface $jobQueue
     * @param Magento_Outbound_TransportInterface $transport
     * @param Magento_Outbound_Message_FactoryInterface $messageFactory
     */
    public function __construct(
        Magento_PubSub_Job_QueueReaderInterface $jobQueue,
        Magento_Outbound_TransportInterface $transport,
        Magento_Outbound_Message_FactoryInterface $messageFactory
    ) {
        $this->_jobQueue = $jobQueue;
        $this->_transport = $transport;
        $this->_messageFactory = $messageFactory;
    }

    /**
     * Process the queue of jobs
     * @return null
     */
    public function handle()
    {
        $job = $this->_jobQueue->poll();
        while (!is_null($job)) {
            $message = $this->_messageFactory->create($job->getSubscription(), $job->getEvent());
            $response = $this->_transport->dispatch($message);
            $job->handleResponse($response);
            $job = $this->_jobQueue->poll();
        }
    }
}