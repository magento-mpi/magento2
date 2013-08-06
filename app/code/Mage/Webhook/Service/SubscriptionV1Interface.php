<?php
/**
 * Webhook Subscription Service - Version 1.
 *
 * This service is used to interact with webhooks subscriptions.
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_Webhook_Service_SubscriptionV1Interface
{

    /**
     * Create a new Subscription
     *
     * @param array $subscriptionData
     * @return array Subscription data
     * @throws Exception|Mage_Core_Exception
     * @throws Mage_Webhook_Exception
     */
    public function create(array $subscriptionData);

    /**
     * Get all Subscriptions associated with a given api user.
     *
     * @param int $apiUserId
     * @return array of Subscription data arrays
     * @throws Exception|Mage_Core_Exception
     * @throws Mage_Webhook_Exception
     */
    public function getAll($apiUserId);

    /**
     * Update a Subscription.
     *
     * @param array $subscriptionData
     * @return array Subscription data
     * @throws Exception|Mage_Core_Exception
     * @throws Mage_Webhook_Exception
     */
    public function update(array $subscriptionData);

    /**
     * Get the details of a specific Subscription.
     *
     * @param int $subscriptionId
     * @return array Subscription data
     * @throws Exception|Mage_Core_Exception
     * @throws Mage_Webhook_Exception
     */
    public function get($subscriptionId);

    /**
     * Delete a Subscription.
     *
     * @param int $subscriptionId
     * @return array Subscription data
     * @throws Exception|Mage_Core_Exception
     * @throws Mage_Webhook_Exception
     */
    public function delete($subscriptionId);

    /**
     * Activate a subscription.
     *
     * @param int $subscriptionId
     * @return array
     * @throws Exception|Mage_Core_Exception
     * @throws Mage_Webhook_Exception
     */
    public function activate($subscriptionId);

    /**
     * De-activate a subscription.
     *
     * @param int $subscriptionId
     * @return array
     * @throws Exception|Mage_Core_Exception
     * @throws Mage_Webhook_Exception
     */
    public function deactivate($subscriptionId);

    /**
     * Revoke a subscription.
     *
     * @param int $subscriptionId
     * @return array
     * @throws Exception|Mage_Core_Exception
     * @throws Mage_Webhook_Exception
     */
    public function revoke($subscriptionId);

    /**
     * Returns trues if a given userId is associated with a subscription
     *
     * @param int $apiUserId
     * @param int $subscriptionId
     * @throws Mage_Webhook_Exception
     */
    public function validateOwnership($apiUserId, $subscriptionId);

}
