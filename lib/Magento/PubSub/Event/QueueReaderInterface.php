<?php
/**
 * Represents Event queue for events that still need to be handled
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PubSub
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_PubSub_Event_QueueReaderInterface
{
    /**
     * Return the top event from the queue.
     *
     * @return Magento_PubSub_EventInterface|null
     */
    public function poll();
}