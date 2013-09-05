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
namespace Magento\PubSub\Job;

class QueueHandler
{
    /**
     * @var \Magento\PubSub\Job\QueueReaderInterface
     */
    protected $_jobQueueReader;

    /**
     * @var \Magento\PubSub\Job\QueueWriterInterface
     */
    protected $_jobQueueWriter;

    /**
     * @var \Magento\Outbound\TransportInterface
     */
    protected $_transport;

    /**
     * @var \Magento\Outbound\Message\FactoryInterface
     */
    protected $_messageFactory;

    /**
     * @param \Magento\PubSub\Job\QueueReaderInterface $jobQueueReader
     * @param \Magento\PubSub\Job\QueueWriterInterface $jobQueueWriter
     * @param \Magento\Outbound\TransportInterface $transport
     * @param \Magento\Outbound\Message\FactoryInterface $messageFactory
     */
    public function __construct(
        \Magento\PubSub\Job\QueueReaderInterface $jobQueueReader,
        \Magento\PubSub\Job\QueueWriterInterface $jobQueueWriter,
        \Magento\Outbound\TransportInterface $transport,
        \Magento\Outbound\Message\FactoryInterface $messageFactory
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
