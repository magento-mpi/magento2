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
interface Magento_PubSub_Message_DispatcherAsyncInterface
{
    /**
     * Dispatch some data on a given topic
     *
     * @param string $topic
     * @param array $data should only contain primitives, no objects.
     */
    public function dispatch($topic, $data);
}