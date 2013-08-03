<?php
/**
 * Stub factory to avoid DI issues.
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PubSub
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_PubSub_Event_Factory implements Magento_PubSub_Event_FactoryInterface
{
    /**
     * Stub won't create an event
     *
     * @param string $topic Topic on which to publish data
     * @param array $data Data to be published.  Should only contain primitives
     * @return Magento_PubSub_EventInterface
     */
    public function create($topic, $data)
    {
        return new Magento_PubSub_Event($topic, $data);
    }
}