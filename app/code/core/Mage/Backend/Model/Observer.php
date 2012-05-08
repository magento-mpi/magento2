<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Backend event observer
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Observer
{

    public function bindLocale($observer)
    {
        if ($locale=$observer->getEvent()->getLocale()) {
            if ($choosedLocale = Mage::getSingleton('Mage_Backend_Model_Session')->getLocale()) {
                $locale->setLocaleCode($choosedLocale);
            }
        }
        return $this;
    }

    /**
     * Prepare massaction separated data
     *
     * @return Mage_Backend_Model_Observer
     */
    public function massactionPrepareKey()
    {
        $request = Mage::app()->getFrontController()->getRequest();
        if ($key = $request->getPost('massaction_prepare_key')) {
            $postData = $request->getPost($key);
            $value = is_array($postData) ? $postData : explode(',', $postData);
            $request->setPost($key, $value ? $value : null);
        }
        return $this;
    }

    /**
     * Clear result of configuration files access level verification in system cache
     *
     * @return Mage_Backend_Model_Observer
     */
    public function clearCacheConfigurationFilesAccessLevelVerification()
    {
        Mage::app()->removeCache(Mage_Adminhtml_Block_Notification_Security::VERIFICATION_RESULT_CACHE_KEY);
        return $this;
    }

    /**
     * Handler for controller_action_predispatch event
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Backend_Model_Observer
     */
    public function actionPreDispatchAdmin($observer)
    {
        $request = Mage::app()->getRequest();

        /** @var $controller Mage_Core_Controller_Varien_Action */
        $controller = $observer->getEvent()->getControllerAction();

        /** @var $auth Mage_Backend_Model_Auth */
        $auth = Mage::getSingleton('Mage_Backend_Model_Auth');

        $requestedActionName = $request->getActionName();
        $openActions = array(
            'forgotpassword',
            'resetpassword',
            'resetpasswordpost',
            'logout',
            'refresh' // captcha refresh
        );
        if (in_array($requestedActionName, $openActions)) {
            $request->setDispatched(true);
        } else {
            if ($auth->getUser()) {
                $auth->getUser()->reload();
            }
            if (!$auth->isLoggedIn()) {
                $isRedirectNeeded = false;
                if ($request->getPost('login')) {
                    $this->_performLogin($controller, $isRedirectNeeded);
                }
                if (!$isRedirectNeeded && !$request->getParam('forwarded')) {
                    if ($request->getParam('isIframe')) {
                        $request->setParam('forwarded', true)
                            ->setControllerName('auth')
                            ->setActionName('deniedIframe')
                            ->setDispatched(false);
                    } else if ($request->getParam('isAjax')) {
                        $request->setParam('forwarded', true)
                            ->setControllerName('auth')
                            ->setActionName('deniedJson')
                            ->setDispatched(false);
                    } else {
                        $request->setParam('forwarded', true)
                            ->setRouteName('adminhtml')
                            ->setControllerName('auth')
                            ->setActionName('login')
                            ->setDispatched(false);
                    }
                }
            }
        }
        $auth->getAuthStorage()->refreshAcl();
        return $this;
    }

    /**
     * Performs login, if user submitted login form
     *
     * @param Mage_Core_Controller_Varien_Action $controller
     * @param bool $isRedirectNeeded
     * @return Mage_Backend_Model_Observer
     */
    protected function _performLogin($controller, &$isRedirectNeeded)
    {
        $isRedirectNeeded = false;

        $request = $controller->getRequest();

        $postLogin  = $request->getPost('login');
        $username   = isset($postLogin['username']) ? $postLogin['username'] : '';
        $password   = isset($postLogin['password']) ? $postLogin['password'] : '';
        $request->setPost('login', null);


        try {
            Mage::getSingleton('Mage_Backend_Model_Auth')->login($username, $password);
            $this->_redirectIfNeededAfterLogin($controller);
        } catch (Mage_Backend_Model_Auth_Exception $e) {
            if (!$request->getParam('messageSent')) {
                Mage::getSingleton('Mage_Backend_Model_Session')->addError(
                    Mage::helper('Mage_Backend_Helper_Data')->__('Invalid User Name or Password.')
                );
                $request->setParam('messageSent', true);
            }
        }

        return $this;
    }

    /**
     * Checks, whether Magento requires redirection after successful admin login, and redirects user, if needed
     *
     * @param Mage_Core_Controller_Varien_Action $controller
     * @return bool
     */
    protected function _redirectIfNeededAfterLogin($controller)
    {
        $requestUri = $this->_getRequestUri($controller->getRequest());
        if (!$requestUri) {
            return false;
        }
        Mage::app()->getResponse()->setRedirect($requestUri);
        $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
        return true;
    }

    /**
     * Checks, whether secret key is required for admin access or request uri is explicitly set, and returns
     * an appropriate url for redirection or null, if none
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return string|null
     */
    protected function _getRequestUri($request)
    {
        /** @var $urlModel Mage_Adminhtml_Model_Url */
        $urlModel = Mage::getSingleton('Mage_Adminhtml_Model_Url');
        if ($urlModel->useSecretKey()) {
            return $urlModel->getUrl('*/*/*', array('_current' => true));
        } elseif ($request) {
            return $request->getRequestUri();
        } else {
            return null;
        }
    }
}
