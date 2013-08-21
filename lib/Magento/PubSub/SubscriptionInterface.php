<?php
/**
 * Represents a subscription to one or more topics
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PubSub
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_PubSub_SubscriptionInterface extends Magento_Outbound_EndpointInterface
{

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_REVOKED = 2;

    /**
     * Returns a list of topics that this Subscription is subscribed to
     *
     * @return string[]
     */
    public function getTopics();

    /**
     * Determines if the subscription is subscribed to a topic.
     *
     * @param string $topic     The topic to check
     * @return boolean          True if subscribed, false otherwise
     */
    public function hasTopic($topic);


    /**
     * Get the status of this endpoint
     *
     * @return int Should match one of the status constants in Magento_PubSub_SubscriptionInterface
     */
    public function getStatus();

    /**
     * Mark this subscription status as deactivated
     *
     * @return Magento_PubSub_SubscriptionInterface The deactivated subscription
     */
    public function deactivate();


    /**
     * Mark this subscription status to activated
     *
     * @return Magento_PubSub_SubscriptionInterface The activated subscription
     */
    public function activate();


    /**
     * Mark this subscription status to revoked
     *
     * @return Magento_PubSub_SubscriptionInterface The revoked subscription
     */
    public function revoke();

    /**
     * Return endpoint with the subscription
     *
     * @return Magento_Outbound_EndpointInterface
     */
    public function getEndpoint();
}
