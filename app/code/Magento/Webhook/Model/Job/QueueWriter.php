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
namespace Magento\Webhook\Model\Job;

class QueueWriter implements \Magento\PubSub\Job\QueueWriterInterface
{
    /** @var \Magento\Webhook\Model\Job\Factory */
    protected $_jobFactory;

    /**
     * Initialize model
     *
     * @param \Magento\Webhook\Model\Job\Factory $jobFactory
     */
    public function __construct(\Magento\Webhook\Model\Job\Factory $jobFactory)
    {
        $this->_jobFactory = $jobFactory;
    }

    /**
     * Adds the job to the queue.
     *
     * @param \Magento\PubSub\JobInterface $job
     * @return null
     */
    public function offer(\Magento\PubSub\JobInterface $job)
    {
        if ($job instanceof \Magento\Webhook\Model\Job) {
            $job->save();
        } else {
            /** @var \Magento\Webhook\Model\Job $magentoJob */
            $magentoJob = $this->_jobFactory->create($job->getSubscription(), $job->getEvent());
            $magentoJob->save();
        }
    }
}
