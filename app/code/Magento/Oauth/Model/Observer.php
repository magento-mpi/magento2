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
     * @param Magento_Oauth_Helper_Data $oauthData
     */
    public function __construct(
        Magento_Oauth_Helper_Data $oauthData
    ) {
        $this->_oauthData = $oauthData;
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
            Mage::app()->getResponse()
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
            Mage::app()->getResponse()
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
            /** @var $session Magento_Backend_Model_Auth_Session */
            $session = Mage::getSingleton('Magento_Backend_Model_Auth_Session');
            $session->addError($observer->getException()->getMessage());

            $userType = Magento_Oauth_Model_Token::USER_TYPE_ADMIN;
            $url = $this->_oauthData->getAuthorizeUrl($userType);
            Mage::app()->getResponse()
                ->setRedirect($url)
                ->sendHeaders()
                ->sendResponse();
            exit();
        }
    }
}
