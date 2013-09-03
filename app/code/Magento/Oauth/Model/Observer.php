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
     * Retrieve oauth_token param from request
     *
     * @return string|null
     */
    protected function _getOauthToken()
    {
        return Mage::helper('Magento_Oauth_Helper_Data')->getOauthToken();
    }

    /**
     * Redirect customer to callback page after login
     *
     * @param \Magento\Event\Observer $observer
     */
    public function afterCustomerLogin(\Magento\Event\Observer $observer)
    {
        if (null !== $this->_getOauthToken()) {
            $userType = Magento_Oauth_Model_Token::USER_TYPE_CUSTOMER;
            $url = Mage::helper('Magento_Oauth_Helper_Data')->getAuthorizeUrl($userType);
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
     * @param \Magento\Event\Observer $observer
     */
    public function afterAdminLogin(\Magento\Event\Observer $observer)
    {
        if (null !== $this->_getOauthToken()) {
            $userType = Magento_Oauth_Model_Token::USER_TYPE_ADMIN;
            $url = Mage::helper('Magento_Oauth_Helper_Data')->getAuthorizeUrl($userType);
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
     * @param \Magento\Event\Observer $observer
     */
    public function afterAdminLoginFailed(\Magento\Event\Observer $observer)
    {
        if (null !== $this->_getOauthToken()) {
            /** @var $session Magento_Backend_Model_Auth_Session */
            $session = Mage::getSingleton('Magento_Backend_Model_Auth_Session');
            $session->addError($observer->getException()->getMessage());

            $userType = Magento_Oauth_Model_Token::USER_TYPE_ADMIN;
            $url = Mage::helper('Magento_Oauth_Helper_Data')->getAuthorizeUrl($userType);
            Mage::app()->getResponse()
                ->setRedirect($url)
                ->sendHeaders()
                ->sendResponse();
            exit();
        }
    }
}
