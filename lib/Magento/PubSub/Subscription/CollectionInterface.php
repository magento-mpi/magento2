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
interface Magento_PubSub_Subscription_CollectionInterface
{
    /**
     * Return all subscriptions by topic
     *
     * @param string $topic
     * @return Magento_PubSub_SubscriptionInterface[]
     */
    public function getSubscriptionsByTopic($topic);
}