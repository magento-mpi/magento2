<?php
/**
 * Stub queue writer to avoid DI issues.
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PubSub
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PubSub\Event;

class QueueWriter implements \Magento\PubSub\Event\QueueWriterInterface
{
    /**
     * Stub that doesn't do anything
     *
     * @param \Magento\PubSub\EventInterface $event
     * @return null
     */
    public function offer(\Magento\PubSub\EventInterface $event)
    {
        return null;
    }
}
