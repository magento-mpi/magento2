<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * oAuth authorize controller
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_AuthorizeController extends Mage_Core_Controller_Front_Action
{
    /**
     * Index action.
     *
     * @return void
     */
    public function indexAction()
    {
        /** @var $server Mage_OAuth_Model_Server */
        $server = Mage::getModel('oauth/server');
        /** @var $session Mage_Customer_Model_Session */
        $session = Mage::getSingleton('customer/session');

        $isException = false;
        try {
            $server->checkAuthorizeRequest();
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Mage_OAuth_Exception $e) {
            $isException = true;
            $session->addException($e, $this->__('An error occurred. Your authorization request is invalid.'));
        } catch (Exception $e) {
            $isException = true;
            $session->addException($e, $this->__('An error occurred.'));
        }

        $this->loadLayout();
        $contentBlock = $this->getLayout()->getBlock('content');
        if ($session->isLoggedIn()) {
            $contentBlock->unsetChild('oauth.authorize.form');
            /** @var $block Mage_OAuth_Block_Authorize_Button */
            $block = $contentBlock->getChild('oauth.authorize.button');
        } else {
            $contentBlock->unsetChild('oauth.authorize.button');
            /** @var $block Mage_OAuth_Block_Authorize */
            $block = $contentBlock->getChild('oauth.authorize.form');
        }

        $block->setToken($this->_getTokenString())->setIsException($isException);
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    /**
     * Confirm token authorization action
     */
    public function confirmAction()
    {
        $response = $this->getResponse();

        /** @var $session Mage_Customer_Model_Session */
        $session = Mage::getSingleton('customer/session');

        /** @var $server Mage_OAuth_Model_Server */
        $server = Mage::getModel('oauth/server');

        try {
            $token = $server->authorizeToken($session->getCustomerId(), Mage_OAuth_Model_Token::USER_TYPE_CUSTOMER);
            $callback = $server->getFullCallbackUrl($token);  //false in case of OOB
            if ($callback) {
                $response->setRedirect($callback);
            } else {
                $response->setBody($token->getVerifier());
            }
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $response->setException($e);
            $session->addException($e, $this->__('Error authorizing token.'));
        }

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    /**
     * Reject token authorization action
     */
    public function rejectAction()
    {
        /** @var $server Mage_OAuth_Model_Server */
        $server = Mage::getModel('oauth/server');
        $token  = $server->checkAuthorizeRequest();
        $url    = $token->getCallbackUrl();
        $url    .= ((strpos($url, '?') === false) ? '?' : '&') . 'oauth_token=' . $token->getToken() . '&denied=1';

        $this->getResponse()->setRedirect($url)->sendResponse();
    }

    /**
     * Retrieve token out of request
     *
     * @return mixed
     */
    protected function _getTokenString()
    {
        return $this->getRequest()->getQuery('oauth_token', null);
    }

    /**
     * Load token data by token from request
     *
     * @return Mage_OAuth_Model_Token
     * @throws Exception
     */
    protected function _loadToken()
    {
        $tokenString = $this->_getTokenString();

        if ($tokenString === null) {
            throw new Exception('Missing token');
        }
        /** @var $token Mage_OAuth_Model_Token */
        $token = Mage::getModel('oauth/token');

        if (!$token->load($tokenString, 'token')->getId()) {
            throw new Exception('Invalid token.');
        }

        return $token;
    }

    /**
     * Load consumer data by token from request
     *
     * @return Mage_OAuth_Model_Consumer
     * @throws Exception
     */
    protected function _loadConsumer()
    {
        $token = $this->_loadToken();

        /** @var $consumer Mage_OAuth_Model_Consumer */
        $consumer = Mage::getModel('oauth/consumer');

        if (!$consumer->load($token->getConsumerId())->getId()) {
            throw new Exception('Invalid consumer.');
        }

        return $consumer;
    }
}
