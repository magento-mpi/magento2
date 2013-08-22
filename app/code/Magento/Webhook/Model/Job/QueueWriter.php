<?php
/**
 * Custom Magento implementation of Job Queue Writer interface, writes jobs to database based queue
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Job_QueueWriter implements Magento_PubSub_Job_QueueWriterInterface
{
    /** @var Magento_Webhook_Model_Job_Factory */
    protected $_jobFactory;

    /**
     * Initialize model
     *
     * @param Magento_Webhook_Model_Job_Factory $jobFactory
     */
    public function __construct(Magento_Webhook_Model_Job_Factory $jobFactory)
    {
        $this->_jobFactory = $jobFactory;
    }

    /**
     * Adds the job to the queue.
     *
     * @param Magento_PubSub_JobInterface $job
     * @return null
     */
    public function offer(Magento_PubSub_JobInterface $job)
    {
        if ($job instanceof Magento_Webhook_Model_Job) {
            $job->save();
        } else {
            /** @var Magento_Webhook_Model_Job $magentoJob */
            $magentoJob = $this->_jobFactory->create($job->getSubscription(), $job->getEvent());
            $magentoJob->save();
        }
    }
}
