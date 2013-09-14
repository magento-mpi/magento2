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
namespace Magento\Oauth\Controller\Adminhtml\Oauth;

class Authorize extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Session name
     *
     * @var string
     */
    protected $_sessionName = 'Magento\Backend\Model\Auth\Session';

    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    protected $_publicActions = array('index', 'simple', 'confirm', 'confirmSimple','reject', 'rejectSimple');

    /**
     * Disable showing of login form
     *
     * @see \Magento\Adminhtml\Model\Observer::actionPreDispatchAdmin() method for explanation
     * @return void
     */
    public function preDispatch()
    {
        $this->getRequest()->setParam('forwarded', true);

        // check login data before it set null in \Magento\Adminhtml\Model\Observer::actionPreDispatchAdmin
        $loginError = $this->_checkLoginIsEmpty();

        parent::preDispatch();

        // call after parent::preDispatch(); to get session started
        if ($loginError) {
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')
                ->addError(__('Please correct the user name or password.'));
            $params = array('_query' => array('oauth_token' => $this->getRequest()->getParam('oauth_token', null)));
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);
            $params = array('_query' => array('oauth_token' => $this->getRequest()->getParam('oauth_token', null)));
            $this->_redirect('*/*/*', $params);
        }
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
     * Index action with a simple design
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
     * Init authorize page
     *
     * @param bool $simple
     * @return \Magento\Oauth\Controller\Adminhtml\Oauth\Authorize
     */
    protected function _initForm($simple = false)
    {
        /** @var $server \Magento\Oauth\Model\Server */
        $server = \Mage::getModel('Magento\Oauth\Model\Server');
        /** @var $session \Magento\Backend\Model\Auth\Session */
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
            /** @var $block \Magento\Oauth\Block\Adminhtml\Oauth\Authorize\Button */
            $block = $contentBlock->getChildBlock('oauth.authorize.button');
        } else {
            $contentBlock->unsetChild('oauth.authorize.button');
            /** @var $block \Magento\Oauth\Block\Adminhtml\Oauth\Authorize */
            $block = $contentBlock->getChildBlock('oauth.authorize.form');
        }

        $block->setIsSimple($simple)
            ->setToken($this->getRequest()->getQuery('oauth_token'))
            ->setHasException($isException);
        return $this;
    }

    /**
     * Init confirm page
     *
     * @param bool $simple
     * @return \Magento\Oauth\Controller\Adminhtml\Oauth\Authorize
     */
    protected function _initConfirmPage($simple = false)
    {
        /** @var $helper \Magento\Oauth\Helper\Data */
        $helper = \Mage::helper('Magento\Oauth\Helper\Data');

        /** @var $session \Magento\Backend\Model\Auth\Session */
        $session = \Mage::getSingleton($this->_sessionName);

        /** @var $user \Magento\User\Model\User */
        $user = $session->getData('user');
        if (!$user) {
            $session->addError(__('Please login to proceed authorization.'));
            $url = $helper->getAuthorizeUrl(\Magento\Oauth\Model\Token::USER_TYPE_ADMIN);
            $this->_redirectUrl($url);
            return $this;
        }

        $this->loadLayout();

        /** @var $block \Magento\Oauth\Block\Adminhtml\Oauth\Authorize */
        $block = $this->getLayout()->getBlock('oauth.authorize.confirm');
        $block->setIsSimple($simple);

        try {
            /** @var $server \Magento\Oauth\Model\Server */
            $server = \Mage::getModel('Magento\Oauth\Model\Server');

            $token = $server->authorizeToken($user->getId(), \Magento\Oauth\Model\Token::USER_TYPE_ADMIN);

            if (($callback = $helper->getFullCallbackUrl($token))) { //false in case of OOB
                $this->getResponse()->setRedirect($callback . ($simple ? '&simple=1' : ''));
                return $this;
            } else {
                $block->setVerifier($token->getVerifier());
                $session->addSuccess(__('Authorization confirmed.'));
            }
        } catch (\Magento\Core\Exception $e) {
            $block->setHasException(true);
            $session->addError($e->getMessage());
        } catch (\Exception $e) {
            $block->setHasException(true);
            $session->addException($e, __('An error occurred on confirm authorize.'));
        }

        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();

        return $this;
    }

    /**
     * Init reject page
     *
     * @param bool $simple
     * @return \Magento\Oauth\Controller\Authorize
     */
    protected function _initRejectPage($simple = false)
    {
        /** @var $server \Magento\Oauth\Model\Server */
        $server = \Mage::getModel('Magento\Oauth\Model\Server');

        /** @var $session \Magento\Backend\Model\Auth\Session */
        $session = \Mage::getSingleton($this->_sessionName);

        $this->loadLayout();

        /** @var $block \Magento\Oauth\Block\Authorize */
        $block = $this->getLayout()->getBlock('oauth.authorize.reject');
        $block->setIsSimple($simple);

        try {
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

        //display exception
        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();

        return $this;
    }

    /**
     * Check is login data has empty login or pass
     * See \Magento\Backend\Model\Auth\Session: there is no any error message if login or password is empty
     *
     * @return boolean
     */
    protected function _checkLoginIsEmpty()
    {
        $error = false;
        $action = $this->getRequest()->getActionName();
        if (($action == 'index' || $action == 'simple') && $this->getRequest()->getPost('login')) {
            $postLogin  = $this->getRequest()->getPost('login');
            $username   = isset($postLogin['username']) ? $postLogin['username'] : '';
            $password   = isset($postLogin['password']) ? $postLogin['password'] : '';
            if (empty($username) || empty($password)) {
                $error = true;
            }
        }
        return $error;
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
        $this->_initConfirmPage();
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
        $this->_initRejectPage();
    }
}
