<?php
/**
 * Represents Job Queue for jobs that still need to be run
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PubSub
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_PubSub_Job_QueueReaderInterface
{
    /**
     * Return the top job from the queue.
     *
     * @return Magento_PubSub_JobInterface|null
     */
    public function poll();
}