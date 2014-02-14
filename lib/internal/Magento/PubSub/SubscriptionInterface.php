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
namespace Magento\PubSub;

interface SubscriptionInterface extends \Magento\Outbound\EndpointInterface
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
     * @return int Should match one of the status constants in \Magento\PubSub\SubscriptionInterface
     */
    public function getStatus();

    /**
     * Mark this subscription status as deactivated
     *
     * @return \Magento\PubSub\SubscriptionInterface The deactivated subscription
     */
    public function deactivate();


    /**
     * Mark this subscription status to activated
     *
     * @return \Magento\PubSub\SubscriptionInterface The activated subscription
     */
    public function activate();


    /**
     * Mark this subscription status to revoked
     *
     * @return \Magento\PubSub\SubscriptionInterface The revoked subscription
     */
    public function revoke();

    /**
     * Return endpoint with the subscription
     *
     * @return \Magento\Outbound\EndpointInterface
     */
    public function getEndpoint();
}
