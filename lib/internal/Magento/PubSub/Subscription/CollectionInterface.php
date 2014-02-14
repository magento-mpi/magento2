<?php
/**
 * Service for querying Subscriptions
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PubSub
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PubSub\Subscription;

interface CollectionInterface
{
    /**
     * Return all subscriptions by topic
     *
     * @param string $topic
     * @return \Magento\PubSub\SubscriptionInterface[]
     */
    public function getSubscriptionsByTopic($topic);
}
