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
class Magento_PubSub_Event_QueueWriter implements Magento_PubSub_Event_QueueWriterInterface
{
    /**
     * Stub that doesn't do anything
     *
     * @param Magento_PubSub_EventInterface $event
     * @return null
     */
    public function offer(Magento_PubSub_EventInterface $event)
    {
        return null;
    }
}