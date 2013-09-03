<?php
/**
 * Guarantees asynchronous delivery of messages being dispatched.
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PubSub
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PubSub\Message;

interface DispatcherAsyncInterface
{
    /**
     * Dispatch some data on a given topic
     *
     * @param string $topic
     * @param array $data should only contain primitives, no objects.
     */
    public function dispatch($topic, $data);
}
