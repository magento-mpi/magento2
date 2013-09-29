<?php
/**
 * Webhook Subscription Service - Version 1.
 *
 * This service is used to interact with webhooks subscriptions.
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Service;

interface SubscriptionV1Interface
{

    /**
     * Create a new Subscription
     *
     * @param array $subscriptionData
     * @return array Subscription data
     * @throws \Exception|\Magento\Core\Exception
     * @throws \Magento\Webhook\Exception
     */
    public function create(array $subscriptionData);

    /**
     * Get all Subscriptions associated with a given api user.
     *
     * @param int $apiUserId
     * @return array of Subscription data arrays
     * @throws \Exception|\Magento\Core\Exception
     * @throws \Magento\Webhook\Exception
     */
    public function getAll($apiUserId);

    /**
     * Update a Subscription.
     *
     * @param array $subscriptionData
     * @return array Subscription data
     * @throws \Exception|\Magento\Core\Exception
     * @throws \Magento\Webhook\Exception
     */
    public function update(array $subscriptionData);

    /**
     * Get the details of a specific Subscription.
     *
     * @param int $subscriptionId
     * @return array Subscription data
     * @throws \Exception|\Magento\Core\Exception
     * @throws \Magento\Webhook\Exception
     */
    public function get($subscriptionId);

    /**
     * Delete a Subscription.
     *
     * @param int $subscriptionId
     * @return array Subscription data
     * @throws \Exception|\Magento\Core\Exception
     * @throws \Magento\Webhook\Exception
     */
    public function delete($subscriptionId);

    /**
     * Activate a subscription.
     *
     * @param int $subscriptionId
     * @return array
     * @throws \Exception|\Magento\Core\Exception
     * @throws \Magento\Webhook\Exception
     */
    public function activate($subscriptionId);

    /**
     * De-activate a subscription.
     *
     * @param int $subscriptionId
     * @return array
     * @throws \Exception|\Magento\Core\Exception
     * @throws \Magento\Webhook\Exception
     */
    public function deactivate($subscriptionId);

    /**
     * Revoke a subscription.
     *
     * @param int $subscriptionId
     * @return array
     * @throws \Exception|\Magento\Core\Exception
     * @throws \Magento\Webhook\Exception
     */
    public function revoke($subscriptionId);

}
