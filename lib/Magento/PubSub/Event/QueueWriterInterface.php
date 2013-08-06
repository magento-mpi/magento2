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
interface Magento_PubSub_Event_QueueWriterInterface
{
    /**
     * Adds the event to the queue.
     *
     * @param Magento_PubSub_EventInterface $event
     * @return null
     */
    public function offer(Magento_PubSub_EventInterface $event);
}