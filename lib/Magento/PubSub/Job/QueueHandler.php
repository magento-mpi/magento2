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
    protected $_jobQueueReader;

    /**
     * @var Magento_PubSub_Job_QueueWriterInterface
     */
    protected $_jobQueueWriter;

    /**
     * @var Magento_Outbound_TransportInterface
     */
    protected $_transport;

    /**
     * @var Magento_Outbound_Message_FactoryInterface
     */
    protected $_messageFactory;

    /**
     * @param Magento_PubSub_Job_QueueReaderInterface $jobQueueReader
     * @param Magento_PubSub_Job_QueueWriterInterface $jobQueueWriter
     * @param Magento_Outbound_TransportInterface $transport
     * @param Magento_Outbound_Message_FactoryInterface $messageFactory
     */
    public function __construct(
        Magento_PubSub_Job_QueueReaderInterface $jobQueueReader,
        Magento_PubSub_Job_QueueWriterInterface $jobQueueWriter,
        Magento_Outbound_TransportInterface $transport,
        Magento_Outbound_Message_FactoryInterface $messageFactory
    ) {
        $this->_jobQueueReader = $jobQueueReader;
        $this->_jobQueueWriter = $jobQueueWriter;
        $this->_transport = $transport;
        $this->_messageFactory = $messageFactory;
    }

    /**
     * Process the queue of jobs
     * @return null
     */
    public function handle()
    {
        $job = $this->_jobQueueReader->poll();
        while (!is_null($job)) {
            $event = $job->getEvent();
            $message = $this->_messageFactory->create($job->getSubscription()->getEndpoint(),
                $event->getTopic(), $event->getBodyData());
            $response = $this->_transport->dispatch($message);
            if ($response->isSuccessful()) {
                $job->complete();
            } else {
                $job->handleFailure();
                $this->_jobQueueWriter->offer($job);
            }
            $job = $this->_jobQueueReader->poll();
        }
    }
}
