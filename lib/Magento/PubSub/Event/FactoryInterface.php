<?php
/**
 * Factory for classes that implement \Magento\PubSub\EventInterface
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PubSub
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PubSub\Event;

interface FactoryInterface
{
    /**
     * Create new event
     *
     * @param string $topic Topic on which to publish data
     * @param array $data Data to be published.  Should only contain primitives
     * @return \Magento\PubSub\EventInterface
     */
    public function create($topic, $data);
}
