<?php
/**
 * Webhook Subscription Service.
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
class Magento_Webhook_Service_SubscriptionV1 implements Magento_Webhook_Service_SubscriptionV1Interface
{
    /** @var Magento_Webhook_Model_Subscription_Factory $_subscriptionFactory */
    private $_subscriptionFactory;

    /** @var Magento_Webhook_Model_Resource_Subscription_Collection $_subscriptionSet */
    private $_subscriptionSet;

    /**
     * @param Magento_Webhook_Model_Subscription_Factory $subscriptionFactory
     * @param Magento_Webhook_Model_Resource_Subscription_Collection $subscriptionSet
     */
    public function __construct(
        Magento_Webhook_Model_Subscription_Factory $subscriptionFactory,
        Magento_Webhook_Model_Resource_Subscription_Collection $subscriptionSet
    ) {
        $this->_subscriptionFactory = $subscriptionFactory;
        $this->_subscriptionSet = $subscriptionSet;
    }

    /**
     * Create a new Subscription
     *
     * @param array $subscriptionData
     * @return array Subscription data
     * @throws Exception|Magento_Core_Exception
     * @throws Magento_Webhook_Exception
     */
    public function create(array $subscriptionData)
    {
        try {
            $subscription = $this->_subscriptionFactory->create($subscriptionData);

            $this->_validateTopics($subscription);

            $subscription->save();

            return $subscription->getData();
        } catch (Magento_Core_Exception $exception) {
            // These messages are already translated, we can simply surface them.
            throw $exception;
        } catch (Exception $exception) {
            // These messages have no translation, we should not expose our internals but may consider logging them.
            throw new Magento_Webhook_Exception(
                __('Unexpected error.  Please contact the administrator.')
            );
        }
    }

    /**
     * Get all Subscriptions associated with a given api user.
     *
     * @param int $apiUserId
     * @throws Exception|Magento_Core_Exception
     * @throws Magento_Webhook_Exception
     * @return array of Subscription data arrays
     */
    public function getAll($apiUserId)
    {
        try {
            $result = array();
            $subscriptions = $this->_subscriptionSet->getApiUserSubscriptions($apiUserId);

            /** @var Magento_Webhook_Model_Subscription $subscription*/
            foreach ($subscriptions as $subscription) {
                $result[] = $subscription->getData();
            }

            return $result;
        } catch (Magento_Core_Exception $e) {
            // These messages are already translated, we can simply surface them.
            throw $e;
        } catch (Exception $e) {
            // These messages have no translation, we should not expose our internals but may consider logging them.
            throw new Magento_Webhook_Exception(
                __('Unexpected error.  Please contact the administrator.')
            );
        }
    }

    /**
     * Update a Subscription.
     *
     * @param array $subscriptionData
     * @return array Subscription data
     * @throws Exception|Magento_Core_Exception
     * @throws Magento_Webhook_Exception
     */
    public function update(array $subscriptionData)
    {
        try {
            $subscription = $this->_loadSubscriptionById($subscriptionData['subscription_id']);
            $subscription->addData($subscriptionData);

            $this->_validateTopics($subscription);

            $subscription->save();

            return $subscription->getData();
        } catch (Magento_Core_Exception $e) {
            // These messages are already translated, we can simply surface them.
            throw $e;
        } catch (Exception $e) {
            // These messages have no translation, we should not expose our internals but may consider logging them.
            throw new Magento_Webhook_Exception(
                __('Unexpected error.  Please contact the administrator.')
            );
        }
    }

    /**
     * Get the details of a specific Subscription.
     *
     * @param int $subscriptionId
     * @return array Subscription data
     * @throws Exception|Magento_Core_Exception
     * @throws Magento_Webhook_Exception
     */
    public function get($subscriptionId)
    {
        try {
            $subscription = $this->_loadSubscriptionById($subscriptionId);
            return $subscription->getData();
        } catch (Magento_Core_Exception $e) {
            // These messages are already translated, we can simply surface them.
            throw $e;
        } catch (Exception $e) {
            // These messages have no translation, we should not expose our internals but may consider logging them.
            throw new Magento_Webhook_Exception(
                __('Unexpected error.  Please contact the administrator.')
            );
        }
    }

    /**
     * Delete a Subscription.
     *
     * @param int $subscriptionId
     * @return array Subscription data
     * @throws Exception|Magento_Core_Exception
     * @throws Magento_Webhook_Exception
     */
    public function delete($subscriptionId)
    {
        try {
            $subscription = $this->_loadSubscriptionById($subscriptionId);
            $subscriptionData = $subscription->getData();

            $subscription->delete();

            return $subscriptionData;
        } catch (Magento_Core_Exception $e) {
            // These messages are already translated, we can simply surface them.
            throw $e;
        } catch (Exception $e) {
            // These messages have no translation, we should not expose our internals but may consider logging them.
            throw new Magento_Webhook_Exception(
                __('Unexpected error.  Please contact the administrator.')
            );
        }
    }

    /**
     * Activate a subscription.
     *
     * @param int $subscriptionId
     * @return array
     * @throws Exception|Magento_Core_Exception
     * @throws Magento_Webhook_Exception
     */
    public function activate($subscriptionId)
    {
        try {
            $subscription = $this->_loadSubscriptionById($subscriptionId);

            $subscription->activate();
            $subscription->save();
            return $subscription->getData();
        } catch (Magento_Core_Exception $e) {
            // These messages are already translated, we can simply surface them.
            throw $e;
        } catch (Exception $e) {
            // These messages have no translation, we should not expose our internals but may consider logging them.
            throw new Magento_Webhook_Exception(
                __('Unexpected error.  Please contact the administrator.')
            );
        }
    }

    /**
     * De-activate a subscription.
     *
     * @param int $subscriptionId
     * @return array
     * @throws Exception|Magento_Core_Exception
     * @throws Magento_Webhook_Exception
     */
    public function deactivate($subscriptionId)
    {
        try {
            $subscription = $this->_loadSubscriptionById($subscriptionId);

            $subscription->deactivate();
            $subscription->save();
            return $subscription->getData();
        } catch (Magento_Core_Exception $e) {
            // These messages are already translated, we can simply surface them.
            throw $e;
        } catch (Exception $e) {
            // These messages have no translation, we should not expose our internals but may consider logging them.
            throw new Magento_Webhook_Exception(
                __('Unexpected error.  Please contact the administrator.')
            );
        }
    }

    /**
     * Revoke a subscription.
     *
     * @param int $subscriptionId
     * @return array
     * @throws Exception|Magento_Core_Exception
     * @throws Magento_Webhook_Exception
     */
    public function revoke($subscriptionId)
    {
        try {
            $subscription = $this->_loadSubscriptionById($subscriptionId);

            $subscription->revoke();
            $subscription->save();
            return $subscription->getData();
        } catch (Magento_Core_Exception $e) {
            // These messages are already translated, we can simply surface them.
            throw $e;
        } catch (Exception $e) {
            // These messages have no translation, we should not expose our internals but may consider logging them.
            throw new Magento_Webhook_Exception(
                __('Unexpected error.  Please contact the administrator.')
            );
        }
    }

    /**
     * Returns trues if a given userId is associated with a subscription
     *
     * @param int $apiUserId
     * @param int $subscriptionId
     * @throws Magento_Webhook_Exception
     */
    public function validateOwnership($apiUserId, $subscriptionId)
    {
        $subscription = $this->_loadSubscriptionById($subscriptionId);
        if ($subscription->getApiUserId() != $apiUserId) {
            throw new Magento_Webhook_Exception(
                __("User with id %1 doesn't have permission to modify subscription %2", $apiUserId, $subscriptionId)
            );
        }
    }

    /**
     * Validates all the topics for a Subscription are Authorized.
     *
     * If invalid topics exists, an exception will be thrown.
     *
     * @param Magento_Webhook_Model_Subscription $subscription
     * @throws Magento_Webhook_Exception
     */
    private function _validateTopics(Magento_Webhook_Model_Subscription $subscription)
    {
        $invalidTopics = $subscription->findRestrictedTopics();
        if (!empty($invalidTopics)) {
            $listOfTopics = implode(', ', $invalidTopics);
            throw new Magento_Webhook_Exception(
                __('The following topics are not authorized: %1', $listOfTopics)
            );
        }
    }

    /**
     * Load subscription by id.
     *
     * @param int $subscriptionId
     * @throws Magento_Webhook_Exception
     * @return Magento_Webhook_Model_Subscription
     */
    protected function _loadSubscriptionById($subscriptionId)
    {
        $subscription = $this->_subscriptionFactory->create()->load($subscriptionId);
        if (!$subscription->getId()) {
            throw new Magento_Webhook_Exception(
                __("Subscription with ID '%1' doesn't exist.", $subscriptionId)
            );
        }
        return $subscription;
    }

}
