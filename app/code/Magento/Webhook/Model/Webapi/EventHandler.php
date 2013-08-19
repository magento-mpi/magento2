<?php
/**
 * Webapi EventHandler that should be notified if any relevant webapi events are received.
 *
 * The event handler will decide what actions must be taken based on the events.
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Webapi_EventHandler
{
    /** @var Magento_Webapi_Model_Resource_Acl_User  */
    private $_resourceAclUser;

    /** @var Magento_Webhook_Model_Resource_Subscription_Collection  */
    private $_subscriptionSet;

    /**
     * @param Magento_Webhook_Model_Resource_Subscription_Collection $subscriptionSet
     * @param Magento_Webapi_Model_Resource_Acl_User $resourceAclUser
     */
    public function __construct(
        Magento_Webhook_Model_Resource_Subscription_Collection $subscriptionSet,
        Magento_Webapi_Model_Resource_Acl_User $resourceAclUser
    ) {
        $this->_subscriptionSet = $subscriptionSet;
        $this->_resourceAclUser = $resourceAclUser;
    }

    /**
     * Notifies the event handler that a webapi user has changed
     *
     * @param  Magento_Webapi_Model_Acl_User $user User object that changed
     */
    public function userChanged($user)
    {
        // call helper that finds and notifies subscription (user_id)
        $this->_validateSubscriptionsForUsers(array($user->getId()));
    }

    /**
     * Notifies the event handler that a webapi role has changed
     *
     * @param  Magento_Webapi_Model_Acl_Role $role Role object that changed
     */
    public function roleChanged($role)
    {
        // get all users that contain this role (role_id)
        $users = $this->_resourceAclUser->getRoleUsers($role->getId());
        
        // for each user, call helper that finds and notifies subscription (user_id)
        $this->_validateSubscriptionsForUsers($users);
    }

    /**
     * Finds all Subscriptions for the given users, and validates that these subscriptions are still valid.
     *
     * @param  array  $userIds users to check against
     */
    protected function _validateSubscriptionsForUsers(array $userIds)
    {
        $subscriptions = $this->_subscriptionSet->getApiUserSubscriptions($userIds);

        /** @var Magento_Webhook_Model_Subscription $subscription */
        foreach ($subscriptions as $subscription) {
            if ($subscription->findRestrictedTopics()) {
                $subscription->deactivate();
                $subscription->save();
            }
        }
    }
}
