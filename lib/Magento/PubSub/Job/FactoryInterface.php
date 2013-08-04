<?php
/**
 * Factory for classes that implement Magento_PubSub_SubscriptionInterface
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PubSub
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_PubSub_Job_FactoryInterface
{
    /**
     * Create Job
     *
     * @param Magento_PubSub_SubscriptionInterface $subscription
     * @param Magento_PubSub_EventInterface $event
     * @return Magento_PubSub_JobInterface|null
     */
    public function create(Magento_PubSub_SubscriptionInterface $subscription, Magento_PubSub_EventInterface $event);
}