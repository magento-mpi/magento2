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
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * oAuth authorize controller
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_Adminhtml_OAuth_AuthorizeController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Session name
     *
     * @var string
     */
    protected $_sessionName = 'admin/session';

    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    public $_publicActions = array('index', 'confirm', 'reject');

    /**
     * Disable showing of login form
     *
     * @see Mage_Admin_Model_Observer::actionPreDispatchAdmin() method for explanation
     * @return void
     */
    public function preDispatch()
    {
        $this->getRequest()->setParam('forwarded', true);
        parent::preDispatch();
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
     * Index action.
     *
     * @return void
     */
    public function popUpAction()
    {
        $this->_initForm(true);
        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();
    }

    /**
     * Init authorize page
     *
     * @param bool $popUp
     * @return Mage_OAuth_Adminhtml_OAuth_AuthorizeController
     */
    protected function _initForm($popUp = false)
    {
        /** @var $server Mage_OAuth_Model_Server */
        $server = Mage::getModel('oauth/server');
        /** @var $session Mage_Admin_Model_Session */
        $session = Mage::getSingleton($this->_sessionName);

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
            /** @var $block Mage_OAuth_Block_Adminhtml_OAuth_Authorize_Button */
            $block = $contentBlock->getChild('oauth.authorize.button');
        } else {
            $contentBlock->unsetChild('oauth.authorize.button');
            /** @var $block Mage_OAuth_Block_Adminhtml_OAuth_Authorize */
            $block = $contentBlock->getChild('oauth.authorize.form');
        }

        $block->setIsPopUp($popUp);

        $block->setToken($this->_getTokenString())->setIsException($isException);

        return $this;
    }

    /**
     * Confirm token authorization action
     */
    public function confirmAction()
    {
        /** @var $session Mage_Admin_Model_Session */
        $session = Mage::getSingleton($this->_sessionName);

        /** @var $server Mage_OAuth_Model_Server */
        $server = Mage::getModel('oauth/server');

        $this->loadLayout();
        /** @var $block Mage_Core_Block_Template */
        $block = $this->getLayout()->getBlock('content')->getChild('oauth.authorize.confirm');

        try {
            /** @var $user Mage_Admin_Model_User */
            $user = $session->getData('user');
            $token = $server->authorizeToken($user->getId(), Mage_OAuth_Model_Token::USER_TYPE_ADMIN);
            $callback = $server->getFullCallbackUrl($token);  //false in case of OOB
            if ($callback) {
                $this->getResponse()->setRedirect($callback);
                return;
            } else {
                $block->setVerifier($token->getVerifier());
            }
        } catch (Mage_Core_Exception $e) {
            $block->setIsException(true);
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $block->setIsException(true);
            $session->addException($e, $this->__('Error authorizing token.'));
        }

        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();
    }

    /**
     * Reject token authorization action
     */
    public function rejectAction()
    {
        /** @var $server Mage_OAuth_Model_Server */
        $server = Mage::getModel('oauth/server');

        /** @var $session Mage_Admin_Model_Session */
        $session = Mage::getSingleton($this->_sessionName);

        try {
            $token = $server->checkAuthorizeRequest();

            $callback = $server->getFullCallbackUrl($token, true);
            if ($callback) {
                $this->_redirectUrl($callback);
                return;
            } else {
                $session->addSuccess($this->__('App authorization declined.'));
            }
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addException($e, $this->__('Error rejecting token.'));
        }

        //display exception
        $this->loadLayout();
        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();
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
}
