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
namespace Magento\Oauth\Model;

class Observer
{
    /**
     * Oauth data
     *
     * @var \Magento\Oauth\Helper\Data
     */
    protected $_oauthData = null;

    /**
     * @param \Magento\Oauth\Helper\Data $oauthData
     */
    public function __construct(
        \Magento\Oauth\Helper\Data $oauthData
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
     * @param \Magento\Event\Observer $observer
     */
    public function afterCustomerLogin(\Magento\Event\Observer $observer)
    {
        if (null !== $this->_getOauthToken()) {
            $userType = \Magento\Oauth\Model\Token::USER_TYPE_CUSTOMER;
            $url = $this->_oauthData->getAuthorizeUrl($userType);
            \Mage::app()->getResponse()
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
            $userType = \Magento\Oauth\Model\Token::USER_TYPE_ADMIN;
            $url = $this->_oauthData->getAuthorizeUrl($userType);
            \Mage::app()->getResponse()
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
            /** @var $session \Magento\Backend\Model\Auth\Session */
            $session = \Mage::getSingleton('Magento\Backend\Model\Auth\Session');
            $session->addError($observer->getException()->getMessage());

            $userType = \Magento\Oauth\Model\Token::USER_TYPE_ADMIN;
            $url = $this->_oauthData->getAuthorizeUrl($userType);
            \Mage::app()->getResponse()
                ->setRedirect($url)
                ->sendHeaders()
                ->sendResponse();
            exit();
        }
    }
}
