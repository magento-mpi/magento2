<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Captcha Observer
 *
 * @category    Mage
 * @package     Mage_Captcha
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Captcha_Model_Observer
{
    /**
     * Customer Session
     *
     * @var Mage_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * CAPTCHA helper
     *
     * @var Mage_Captcha_Helper_Data
     */
    protected $_helper;

    /**
     * URL manager
     *
     * @var Mage_Core_Model_Url
     */
    protected $_urlManager;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @param Mage_Customer_Model_Session $customerSession
     * @param Mage_Captcha_Helper_Data $helper
     * @param Mage_Core_Model_Url $urlManager
     * @param Magento_Filesystem $filesystem
     */
    public function __construct(
        Mage_Customer_Model_Session $customerSession,
        Mage_Captcha_Helper_Data $helper,
        Mage_Core_Model_Url $urlManager,
        Magento_Filesystem $filesystem
    ) {
        $this->_customerSession = $customerSession;
        $this->_helper = $helper;
        $this->_urlManager = $urlManager;
        $this->_filesystem = $filesystem;
    }

    /**
     * Check Captcha On Forgot Password Page
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function checkForgotpassword($observer)
    {
        $formId = 'user_forgotpassword';
        $captchaModel = $this->_helper->getCaptcha($formId);
        if ($captchaModel->isRequired()) {
            $controller = $observer->getControllerAction();
            if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                Mage::getSingleton('Mage_Customer_Model_Session')->addError(__('Incorrect CAPTCHA'));
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                $controller->getResponse()->setRedirect(Mage::getUrl('*/*/forgotpassword'));
            }
        }
        return $this;
    }

    /**
     * Check CAPTCHA on Contact Us page
     *
     * @param Varien_Event_Observer $observer
     */
    public function checkContactUsForm($observer)
    {
        $formId = 'contact_us';
        $captcha = $this->_helper->getCaptcha($formId);
        if ($captcha->isRequired()) {
            $controller = $observer->getControllerAction();
            if (!$captcha->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                $this->_customerSession->addError(__('Incorrect CAPTCHA.'));
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                $controller->getResponse()->setRedirect($this->_urlManager->getUrl('contacts/index/index'));
            }
        }
    }

    /**
     * Check Captcha On User Login Page
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function checkUserLogin($observer)
    {
        $formId = 'user_login';
        $captchaModel = $this->_helper->getCaptcha($formId);
        $controller = $observer->getControllerAction();
        $loginParams = $controller->getRequest()->getPost('login');
        $login = array_key_exists('username', $loginParams) ? $loginParams['username'] : null;
        if ($captchaModel->isRequired($login)) {
            $word = $this->_getCaptchaString($controller->getRequest(), $formId);
            if (!$captchaModel->isCorrect($word)) {
                Mage::getSingleton('Mage_Customer_Model_Session')->addError(__('Incorrect CAPTCHA'));
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                Mage::getSingleton('Mage_Customer_Model_Session')->setUsername($login);
                $beforeUrl = Mage::getSingleton('Mage_Customer_Model_Session')->getBeforeAuthUrl();
                $url =  $beforeUrl ? $beforeUrl : Mage::helper('Mage_Customer_Helper_Data')->getLoginUrl();
                $controller->getResponse()->setRedirect($url);
            }
        }
        $captchaModel->logAttempt($login);
        return $this;
    }

    /**
     * Check Captcha On Register User Page
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function checkUserCreate($observer)
    {
        $formId = 'user_create';
        $captchaModel = $this->_helper->getCaptcha($formId);
        if ($captchaModel->isRequired()) {
            $controller = $observer->getControllerAction();
            if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                Mage::getSingleton('Mage_Customer_Model_Session')->addError(__('Incorrect CAPTCHA'));
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                Mage::getSingleton('Mage_Customer_Model_Session')->setCustomerFormData($controller->getRequest()->getPost());
                $controller->getResponse()->setRedirect(Mage::getUrl('*/*/create'));
            }
        }
        return $this;
    }

    /**
     * Check Captcha On Checkout as Guest Page
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function checkGuestCheckout($observer)
    {
        $formId = 'guest_checkout';
        $captchaModel = $this->_helper->getCaptcha($formId);
        $checkoutMethod = Mage::getSingleton('Mage_Checkout_Model_Type_Onepage')->getQuote()->getCheckoutMethod();
        if ($checkoutMethod == Mage_Checkout_Model_Type_Onepage::METHOD_GUEST) {
            if ($captchaModel->isRequired()) {
                $controller = $observer->getControllerAction();
                if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                    $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    $result = array('error' => 1, 'message' => __('Incorrect CAPTCHA'));
                    $controller->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode($result));
                }
            }
        }
        return $this;
    }

    /**
     * Check Captcha On Checkout Register Page
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function checkRegisterCheckout($observer)
    {
        $formId = 'register_during_checkout';
        $captchaModel = $this->_helper->getCaptcha($formId);
        $checkoutMethod = Mage::getSingleton('Mage_Checkout_Model_Type_Onepage')->getQuote()->getCheckoutMethod();
        if ($checkoutMethod == Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER) {
            if ($captchaModel->isRequired()) {
                $controller = $observer->getControllerAction();
                if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                    $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    $result = array('error' => 1, 'message' => __('Incorrect CAPTCHA'));
                    $controller->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode($result));
                }
            }
        }
        return $this;
    }

    /**
     * Check Captcha On User Login Backend Page
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function checkUserLoginBackend($observer)
    {
        $formId = 'backend_login';
        $captchaModel = $this->_helper->getCaptcha($formId);
        $login = $observer->getEvent()->getUsername();
        if ($captchaModel->isRequired($login)) {
            if (!$captchaModel->isCorrect($this->_getCaptchaString(Mage::app()->getRequest(), $formId))) {
                $captchaModel->logAttempt($login);
                throw new Mage_Backend_Model_Auth_Plugin_Exception(
                    __('Incorrect CAPTCHA.')
                );
            }
        }
        $captchaModel->logAttempt($login);
        return $this;
    }

    /**
     * Returns backend session
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getBackendSession()
    {
        return Mage::getSingleton('Mage_Adminhtml_Model_Session');
    }

    /**
     * Check Captcha On User Login Backend Page
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function checkUserForgotPasswordBackend($observer)
    {
        $formId = 'backend_forgotpassword';
        $captchaModel = $this->_helper->getCaptcha($formId);
        $controller = $observer->getControllerAction();
        $email = (string) $observer->getControllerAction()->getRequest()->getParam('email');
        $params = $observer->getControllerAction()->getRequest()->getParams();

        if (!empty($email) && !empty($params)){
            if ($captchaModel->isRequired()){
                if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                    $this->_getBackendSession()->setEmail((string) $controller->getRequest()->getPost('email'));
                    $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    $this->_getBackendSession()->addError(__('Incorrect CAPTCHA'));
                    $controller->getResponse()->setRedirect($controller->getUrl('*/*/forgotpassword', array('_nosecret' => true)));
                }
            }
        }
        return $this;
    }

    /**
     * Reset Attempts For Frontend
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function resetAttemptForFrontend($observer)
    {
        return $this->_resetAttempt($observer->getModel()->getEmail());
    }

    /**
     * Reset Attempts For Backend
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function resetAttemptForBackend($observer)
    {
        return $this->_resetAttempt($observer->getUser()->getUsername());
    }

    /**
     * Delete Unnecessary logged attempts
     *
     * @return Mage_Captcha_Model_Observer
     */
    public function deleteOldAttempts()
    {
        Mage::getResourceModel('Mage_Captcha_Model_Resource_Log')->deleteOldAttempts();
        return $this;
    }

    /**
     * Delete Expired Captcha Images
     *
     * @return Mage_Captcha_Model_Observer
     */
    public function deleteExpiredImages()
    {
        foreach (Mage::app()->getWebsites(true) as $website) {
            $expire = time() - $this->_helper->getConfigNode('timeout', $website->getDefaultStore()) * 60;
            $imageDirectory = $this->_helper->getImgDir($website);
            foreach ($this->_filesystem->getNestedKeys($imageDirectory) as $filePath) {
                if ($this->_filesystem->isFile($filePath)
                    && pathinfo($filePath, PATHINFO_EXTENSION) == 'png'
                    && $this->_filesystem->getMTime($filePath) < $expire) {
                    $this->_filesystem->delete($filePath);
                }
            }
        }
        return $this;
    }

    /**
     * Reset Attempts
     *
     * @param string $login
     * @return Mage_Captcha_Model_Observer
     */
    protected function _resetAttempt($login)
    {
        Mage::getResourceModel('Mage_Captcha_Model_Resource_Log')->deleteUserAttempts($login);
        return $this;
    }

    /**
     * Get Captcha String
     *
     * @param Varien_Object $request
     * @param string $formId
     * @return string
     */
    protected function _getCaptchaString($request, $formId)
    {
        $captchaParams = $request->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
        return $captchaParams[$formId];
    }
}
