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
 * Manage consumers controller
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_Adminhtml_OAuth_AuthorizeController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    public $_publicActions = array('index');

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
        /** @var $server Mage_OAuth_Model_Server */
        $server = Mage::getModel('oauth/server');
        $server->checkAuthorizeRequest();

        /** @var $session Mage_Admin_Model_Session */
        $session = Mage::getSingleton('admin/session');

        $this->loadLayout();
        $contentBlock = $this->getLayout()->getBlock('content');
        if ($session->isLoggedIn()) {
            $contentBlock->unsetChild('oauth.authorize.form');
            /** @var $block Mage_OAuth_Block_Authorize_Button */
            $block = $contentBlock->getChild('oauth.authorize.button');
            $block->setUserType(Mage_Api2_Model_Auth::USER_TYPE_ADMIN)
                  ->setToken($this->_getTokenString());
        } else {
            $contentBlock->unsetChild('oauth.authorize.button');
        }

        $this->renderLayout();
    }

    /**
     * Confirm token authorization action
     */
    public function confirmAction()
    {
        /** @var $session Mage_Admin_Model_Session */
        $session = Mage::getSingleton('admin/session');

        /** @var $server Mage_OAuth_Model_Server */
        $server = Mage::getModel('oauth/server');
        $token = $server->authorizeToken($session->getUser()->getId(), Mage_OAuth_Model_Token::USER_TYPE_ADMIN);

        $callback = $server->getFullCallbackUrl($token);  //false in case of OOB
        $response = $this->getResponse();
        if ($callback) {
            $response->setRedirect($callback);
        } else {
            $response->setBody($token->getVerifier());
        }
        $response->sendResponse();
    }

    /**
     * Reject token authorization action
     */
    public function rejectAction()
    {
        /** @var $server Mage_OAuth_Model_Server */
        $server = Mage::getModel('oauth/server');
        $server->checkAuthorizeRequest();

        $token = $this->_loadToken();
        //$token = $server->rejectToken();

        $tokenString = $this->_getTokenString();
        $delimiter = (strpos($tokenString, '?')===false)   ?'?'  :'&';

        $url = $token->getCallbackUrl();
        $url.= $delimiter.'oauth_token='.$tokenString.'&denied=1';

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
}
