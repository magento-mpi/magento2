<?php
/**
 * Represents Job queue writer for the jobs
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PubSub
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PubSub\Job;

interface QueueWriterInterface
{
    /**
     * Adds the job to the queue.
     *
     * @param \Magento\PubSub\JobInterface $job
     * @return null
     */
    public function offer(\Magento\PubSub\JobInterface $job);
}
