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
interface Magento_PubSub_Job_QueueWriterInterface
{
    /**
     * Adds the job to the queue.
     *
     * @param Magento_PubSub_JobInterface $job
     * @return null
     */
    public function offer(Magento_PubSub_JobInterface $job);
}