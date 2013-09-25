<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * OAuth observer
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Oauth_Model_Observer
{
    /**
     * Oauth data
     *
     * @var Magento_Oauth_Helper_Data
     */
    protected $_oauthData = null;

    /**
     * Core App model
     *
     * @var Magento_Core_Model_App
     */
    protected $_app = null;

    /**
     * Backend Auth Session
     *
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_backendSession = null;

    /**
     * @param Magento_Oauth_Helper_Data $oauthData
     * @param Magento_Core_Model_App $app
     * @param Magento_Backend_Model_Auth_Session $backendSession
     */
    public function __construct(
        Magento_Oauth_Helper_Data $oauthData,
        Magento_Core_Model_App $app,
        Magento_Backend_Model_Auth_Session $backendSession
    ) {
        $this->_oauthData = $oauthData;
        $this->_app = $app;
    }

    /**
     * Retrieve oauth_token param from request
     *
     * @return string|null
     */
    protected function _getOauthToken()
    {
        return $this->_oauthData->getOauthToken();
    }

    /**
     * Redirect customer to callback page after login
     *
     * @param Magento_Event_Observer $observer
     */
    public function afterCustomerLogin(Magento_Event_Observer $observer)
    {
        if (null !== $this->_getOauthToken()) {
            $userType = Magento_Oauth_Model_Token::USER_TYPE_CUSTOMER;
            $url = $this->_oauthData->getAuthorizeUrl($userType);
            $this->_app->getResponse()
                ->setRedirect($url)
                ->sendHeaders()
                ->sendResponse();
            exit();
        }
    }

    /**
     * Redirect admin to authorize controller after login success
     *
     * @param Magento_Event_Observer $observer
     */
    public function afterAdminLogin(Magento_Event_Observer $observer)
    {
        if (null !== $this->_getOauthToken()) {
            $userType = Magento_Oauth_Model_Token::USER_TYPE_ADMIN;
            $url = $this->_oauthData->getAuthorizeUrl($userType);
            $this->_app->getResponse()
                ->setRedirect($url)
                ->sendHeaders()
                ->sendResponse();
            exit();
        }
    }

    /**
     * Redirect admin to authorize controller after login fail
     *
     * @param Magento_Event_Observer $observer
     */
    public function afterAdminLoginFailed(Magento_Event_Observer $observer)
    {
        if (null !== $this->_getOauthToken()) {
            $this->_backendSession->addError($observer->getException()->getMessage());

            $userType = Magento_Oauth_Model_Token::USER_TYPE_ADMIN;
            $url = $this->_oauthData->getAuthorizeUrl($userType);
            $this->_app->getResponse()
                ->setRedirect($url)
                ->sendHeaders()
                ->sendResponse();
            exit();
        }
    }
}
