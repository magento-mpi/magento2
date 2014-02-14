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
namespace Magento\PubSub\Job;

interface QueueReaderInterface
{
    /**
     * Return the top job from the queue.
     *
     * @return \Magento\PubSub\JobInterface|null
     */
    public function poll();
}
