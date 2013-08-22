<?php
/**
 * Representation of Magento_PubSub_SubscriptionInterface with data from Magento database
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_User implements Magento_Outbound_UserInterface
{
    /** @var Magento_Webapi_Model_Acl_User  */
    private $_user;

    /**
     * Used to check that this user has proper permissions
     *
     * @var Magento_Authorization
     */
    private $_authorization;

    /**
     * @param Magento_Webapi_Model_Acl_User_Factory $userFactory
     * @param Magento_Webapi_Model_Authorization_Role_Locator_Factory $roleLocatorFactory
     * @param Magento_Webapi_Model_Authorization_Policy_Acl $aclPolicy
     * @param Magento_Authorization_Factory $authorizationFactory
     * @param string $webapiUserId
     */
    public function __construct(
        Magento_Webapi_Model_Acl_User_Factory $userFactory,
        Magento_Webapi_Model_Authorization_Role_Locator_Factory $roleLocatorFactory,
        Magento_Webapi_Model_Authorization_Policy_Acl $aclPolicy,
        Magento_Authorization_Factory $authorizationFactory,
        $webapiUserId
    ) {
        $this->_user = $userFactory->create();
        $this->_user->load($webapiUserId);
        $roleLocator = $roleLocatorFactory->create(array(
            'data' => array('roleId' => $this->_user->getRoleId())
        ));

        $this->_authorization = $authorizationFactory->create(array(
            'aclPolicy' => $aclPolicy,
            'roleLocator' => $roleLocator
        ));
    }

    /**
     * Returns a shared secret known only by Magento and this user
     *
     * @return string A shared secret that both the user and Magento know about
     */
    public function getSharedSecret()
    {
        return $this->_user->getSecret();
    }

    /**
     * Checks whether this user has permission for the given topic
     *
     * @param string $topic Topic to check
     * @return bool True if permissions exist
     */
    public function hasPermission($topic)
    {
        return $this->_authorization->isAllowed($topic);
    }
}
