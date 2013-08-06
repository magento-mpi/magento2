<?php
/**
 * Factory for classes that implement Magento_PubSub_EventInterface
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PubSub
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_PubSub_Event_FactoryInterface
{
    /**
     * Create new event
     *
     * @param string $topic Topic on which to publish data
     * @param array $data Data to be published.  Should only contain primitives
     * @return Magento_PubSub_EventInterface
     */
    public function create($topic, $data);
}