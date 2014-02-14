<?php
/**
 * Represents Event queue writer for events
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PubSub
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PubSub\Event;

interface QueueWriterInterface
{
    /**
     * Adds the event to the queue.
     *
     * @param \Magento\PubSub\EventInterface $event
     * @return null
     */
    public function offer(\Magento\PubSub\EventInterface $event);
}
