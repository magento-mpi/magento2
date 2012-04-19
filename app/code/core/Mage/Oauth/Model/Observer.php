<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * OAuth observer
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oauth_Model_Observer
{
    /**
     * Retrieve oauth_token param from request
     *
     * @return string|null
     */
    protected function _getOauthToken()
    {
        return Mage::helper('Mage_Oauth_Helper_Data')->getOauthToken();
    }

    /**
     * Redirect customer to callback page after login
     *
     * @param Varien_Event_Observer $observer
     */
    public function afterCustomerLogin(Varien_Event_Observer $observer)
    {
        if (null !== $this->_getOauthToken()) {
            $userType = Mage_Oauth_Model_Token::USER_TYPE_CUSTOMER;
            $url = Mage::helper('Mage_Oauth_Helper_Data')->getAuthorizeUrl($userType);
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
     * @param Varien_Event_Observer $observer
     */
    public function afterAdminLogin(Varien_Event_Observer $observer)
    {
        if (null !== $this->_getOauthToken()) {
            $userType = Mage_Oauth_Model_Token::USER_TYPE_ADMIN;
            $url = Mage::helper('Mage_Oauth_Helper_Data')->getAuthorizeUrl($userType);
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
     * @param Varien_Event_Observer $observer
     */
    public function afterAdminLoginFailed(Varien_Event_Observer $observer)
    {
        if (null !== $this->_getOauthToken()) {
            /** @var $session Mage_Admin_Model_Session */
            $session = Mage::getSingleton('Mage_Admin_Model_Session');
            $session->addError($observer->getException()->getMessage());

            $userType = Mage_Oauth_Model_Token::USER_TYPE_ADMIN;
            $url = Mage::helper('Mage_Oauth_Helper_Data')->getAuthorizeUrl($userType);
            Mage::app()->getResponse()
                ->setRedirect($url)
                ->sendHeaders()
                ->sendResponse();
            exit();
        }
    }
}
