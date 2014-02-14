<?php
/**
 * Factory for classes that implement \Magento\PubSub\SubscriptionInterface
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PubSub
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PubSub\Job;

interface FactoryInterface
{
    /**
     * Create Job
     *
     * @param \Magento\PubSub\SubscriptionInterface $subscription
     * @param \Magento\PubSub\EventInterface $event
     * @return \Magento\PubSub\JobInterface|null
     */
    public function create(\Magento\PubSub\SubscriptionInterface $subscription, \Magento\PubSub\EventInterface $event);
}
