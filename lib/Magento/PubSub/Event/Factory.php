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
namespace Magento\PubSub\Event;

class Factory implements \Magento\PubSub\Event\FactoryInterface
{
    /**
     * Stub won't create an event
     *
     * @param string $topic Topic on which to publish data
     * @param array $data Data to be published.  Should only contain primitives
     * @return \Magento\PubSub\EventInterface
     */
    public function create($topic, $data)
    {
        return new \Magento\PubSub\Event($topic, $data);
    }
}
