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
 * oAuth authorize controller
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Oauth\Controller;

class Authorize extends \Magento\Core\Controller\Front\Action
{
    /**
     * Session name
     *
     * @var string
     */
    protected $_sessionName = 'customer/session';

    /**
     * Init authorize page
     *
     * @param bool $simple      Is simple page?
     * @return \Magento\Oauth\Controller\Authorize
     */
    protected function _initForm($simple = false)
    {
        /** @var $server \Magento\Oauth\Model\Server */
        $server = \Mage::getModel('\Magento\Oauth\Model\Server');
        /** @var $session \Magento\Customer\Model\Session */
        $session = \Mage::getSingleton($this->_sessionName);

        $isException = false;
        try {
            $server->checkAuthorizeRequest();
        } catch (\Magento\Core\Exception $e) {
            $session->addError($e->getMessage());
        } catch (\Magento\Oauth\Exception $e) {
            $isException = true;
            $session->addException($e, __('An error occurred. Your authorization request is invalid.'));
        } catch (\Exception $e) {
            $isException = true;
            $session->addException($e, __('An error occurred.'));
        }

        $this->loadLayout();
        $layout = $this->getLayout();
        $logged = $session->isLoggedIn();

        $contentBlock = $layout->getBlock('content');
        if ($logged) {
            $contentBlock->unsetChild('oauth.authorize.form');
            /** @var $block \Magento\Oauth\Block\Authorize\Button */
            $block = $contentBlock->getChildBlock('oauth.authorize.button');
        } else {
            $contentBlock->unsetChild('oauth.authorize.button');
            /** @var $block \Magento\Oauth\Block\Authorize */
            $block = $contentBlock->getChildBlock('oauth.authorize.form');
        }

        /** @var $helper \Magento\Core\Helper\Url */
        $helper = \Mage::helper('Magento\Core\Helper\Url');
        $session->setAfterAuthUrl(\Mage::getUrl('customer/account/login', array('_nosid' => true)))
                ->setBeforeAuthUrl($helper->getCurrentUrl());

        $block->setIsSimple($simple)
            ->setToken($this->getRequest()->getQuery('oauth_token'))
            ->setHasException($isException);
        return $this;
    }

    /**
     * Init confirm page
     *
     * @param bool $simple      Is simple page?
     * @return \Magento\Oauth\Controller\Authorize
     */
    protected function _initConfirmPage($simple = false)
    {
        /** @var $helper \Magento\Oauth\Helper\Data */
        $helper = \Mage::helper('Magento\Oauth\Helper\Data');

        /** @var $session \Magento\Customer\Model\Session */
        $session = \Mage::getSingleton($this->_sessionName);
        if (!$session->getCustomerId()) {
            $session->addError(__('Please login to proceed authorization.'));
            $url = $helper->getAuthorizeUrl(\Magento\Oauth\Model\Token::USER_TYPE_CUSTOMER);
            $this->_redirectUrl($url);
            return $this;
        }

        $this->loadLayout();

        /** @var $block \Magento\Oauth\Block\Authorize */
        $block = $this->getLayout()->getBlock('oauth.authorize.confirm');
        $block->setIsSimple($simple);

        try {
            /** @var $server \Magento\Oauth\Model\Server */
            $server = \Mage::getModel('\Magento\Oauth\Model\Server');

            /** @var $token \Magento\Oauth\Model\Token */
            $token = $server->authorizeToken($session->getCustomerId(), \Magento\Oauth\Model\Token::USER_TYPE_CUSTOMER);

            if (($callback = $helper->getFullCallbackUrl($token))) { //false in case of OOB
                $this->_redirectUrl($callback . ($simple ? '&simple=1' : ''));
                return $this;
            } else {
                $block->setVerifier($token->getVerifier());
                $session->addSuccess(__('Authorization confirmed.'));
            }
        } catch (\Magento\Core\Exception $e) {
            $session->addError($e->getMessage());
        } catch (\Magento\Oauth\Exception $e) {
            $session->addException($e, __('An error occurred. Your authorization request is invalid.'));
        } catch (\Exception $e) {
            $session->addException($e, __('An error occurred on confirm authorize.'));
        }

        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();

        return $this;
    }

    /**
     * Init reject page
     *
     * @param bool $simple      Is simple page?
     * @return \Magento\Oauth\Controller\Authorize
     */
    protected function _initRejectPage($simple = false)
    {
        $this->loadLayout();

        /** @var $session \Magento\Customer\Model\Session */
        $session = \Mage::getSingleton($this->_sessionName);
        try {
            /** @var $server \Magento\Oauth\Model\Server */
            $server = \Mage::getModel('\Magento\Oauth\Model\Server');

            /** @var $block \Magento\Oauth\Block\Authorize */
            $block = $this->getLayout()->getBlock('oauth.authorize.reject');
            $block->setIsSimple($simple);

            /** @var $token \Magento\Oauth\Model\Token */
            $token = $server->checkAuthorizeRequest();
            /** @var $helper \Magento\Oauth\Helper\Data */
            $helper = \Mage::helper('Magento\Oauth\Helper\Data');

            if (($callback = $helper->getFullCallbackUrl($token, true))) {
                $this->_redirectUrl($callback . ($simple ? '&simple=1' : ''));
                return $this;
            } else {
                $session->addNotice(__('The application access request is rejected.'));
            }
        } catch (\Magento\Core\Exception $e) {
            $session->addError($e->getMessage());
        } catch (\Exception $e) {
            $session->addException($e, __('An error occurred on reject authorize.'));
        }

        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();

        return $this;
    }

    /**
     * Index action.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_initForm();
        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();
    }

    /**
     * OAuth authorize or allow decline access simple page
     *
     * @return void
     */
    public function simpleAction()
    {
        $this->_initForm(true);
        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();
    }

    /**
     * Confirm token authorization action
     */
    public function confirmAction()
    {
        $this->_initConfirmPage();
    }

    /**
     * Confirm token authorization simple page
     */
    public function confirmSimpleAction()
    {
        $this->_initConfirmPage(true);
    }

    /**
     * Reject token authorization action
     */
    public function rejectAction()
    {
        $this->_initRejectPage();
    }

    /**
     * Reject token authorization simple page
     */
    public function rejectSimpleAction()
    {
        $this->_initRejectPage(true);
    }
}
