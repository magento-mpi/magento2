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
namespace Magento\PubSub\Event;

interface QueueReaderInterface
{
    /**
     * Return the top event from the queue.
     *
     * @return \Magento\PubSub\EventInterface|null
     */
    public function poll();
}
