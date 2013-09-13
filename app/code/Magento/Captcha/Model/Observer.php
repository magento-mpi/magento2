<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Captcha Observer
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Captcha_Model_Observer
{
    /**
     * Customer Session
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * CAPTCHA helper
     *
     * @var Magento_Captcha_Helper_Data
     */
    protected $_helper;

    /**
     * URL manager
     *
     * @var Magento_Core_Model_Url
     */
    protected $_urlManager;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * Customer data
     *
     * @var Magento_Customer_Helper_Data
     */
    protected $_customerData = null;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Customer_Helper_Data $customerData
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Captcha_Helper_Data $helper
     * @param Magento_Core_Model_Url $urlManager
     * @param Magento_Filesystem $filesystem
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Customer_Helper_Data $customerData,
        Magento_Customer_Model_Session $customerSession,
        Magento_Captcha_Helper_Data $helper,
        Magento_Core_Model_Url $urlManager,
        Magento_Filesystem $filesystem
    ) {
        $this->_coreData = $coreData;
        $this->_customerData = $customerData;
        $this->_customerSession = $customerSession;
        $this->_helper = $helper;
        $this->_urlManager = $urlManager;
        $this->_filesystem = $filesystem;
    }

    /**
     * Check Captcha On Forgot Password Page
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Captcha_Model_Observer
     */
    public function checkForgotpassword($observer)
    {
        $formId = 'user_forgotpassword';
        $captchaModel = $this->_helper->getCaptcha($formId);
        if ($captchaModel->isRequired()) {
            $controller = $observer->getControllerAction();
            if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                Mage::getSingleton('Magento_Customer_Model_Session')->addError(__('Incorrect CAPTCHA'));
                $controller->setFlag('', Magento_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                $controller->getResponse()->setRedirect(Mage::getUrl('*/*/forgotpassword'));
            }
        }
        return $this;
    }

    /**
     * Check CAPTCHA on Contact Us page
     *
     * @param Magento_Event_Observer $observer
     */
    public function checkContactUsForm($observer)
    {
        $formId = 'contact_us';
        $captcha = $this->_helper->getCaptcha($formId);
        if ($captcha->isRequired()) {
            $controller = $observer->getControllerAction();
            if (!$captcha->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                $this->_customerSession->addError(__('Incorrect CAPTCHA.'));
                $controller->setFlag('', Magento_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                $controller->getResponse()->setRedirect($this->_urlManager->getUrl('contacts/index/index'));
            }
        }
    }

    /**
     * Check Captcha On User Login Page
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Captcha_Model_Observer
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
                Mage::getSingleton('Magento_Customer_Model_Session')->addError(__('Incorrect CAPTCHA'));
                $controller->setFlag('', Magento_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                Mage::getSingleton('Magento_Customer_Model_Session')->setUsername($login);
                $beforeUrl = Mage::getSingleton('Magento_Customer_Model_Session')->getBeforeAuthUrl();
                $url =  $beforeUrl ? $beforeUrl : $this->_customerData->getLoginUrl();
                $controller->getResponse()->setRedirect($url);
            }
        }
        $captchaModel->logAttempt($login);
        return $this;
    }

    /**
     * Check Captcha On Register User Page
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Captcha_Model_Observer
     */
    public function checkUserCreate($observer)
    {
        $formId = 'user_create';
        $captchaModel = $this->_helper->getCaptcha($formId);
        if ($captchaModel->isRequired()) {
            $controller = $observer->getControllerAction();
            if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                Mage::getSingleton('Magento_Customer_Model_Session')->addError(__('Incorrect CAPTCHA'));
                $controller->setFlag('', Magento_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                Mage::getSingleton('Magento_Customer_Model_Session')->setCustomerFormData($controller->getRequest()->getPost());
                $controller->getResponse()->setRedirect(Mage::getUrl('*/*/create'));
            }
        }
        return $this;
    }

    /**
     * Check Captcha On Checkout as Guest Page
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Captcha_Model_Observer
     */
    public function checkGuestCheckout($observer)
    {
        $formId = 'guest_checkout';
        $captchaModel = $this->_helper->getCaptcha($formId);
        $checkoutMethod = Mage::getSingleton('Magento_Checkout_Model_Type_Onepage')->getQuote()->getCheckoutMethod();
        if ($checkoutMethod == Magento_Checkout_Model_Type_Onepage::METHOD_GUEST) {
            if ($captchaModel->isRequired()) {
                $controller = $observer->getControllerAction();
                if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                    $controller->setFlag('', Magento_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    $result = array('error' => 1, 'message' => __('Incorrect CAPTCHA'));
                    $controller->getResponse()->setBody($this->_coreData->jsonEncode($result));
                }
            }
        }
        return $this;
    }

    /**
     * Check Captcha On Checkout Register Page
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Captcha_Model_Observer
     */
    public function checkRegisterCheckout($observer)
    {
        $formId = 'register_during_checkout';
        $captchaModel = $this->_helper->getCaptcha($formId);
        $checkoutMethod = Mage::getSingleton('Magento_Checkout_Model_Type_Onepage')->getQuote()->getCheckoutMethod();
        if ($checkoutMethod == Magento_Checkout_Model_Type_Onepage::METHOD_REGISTER) {
            if ($captchaModel->isRequired()) {
                $controller = $observer->getControllerAction();
                if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                    $controller->setFlag('', Magento_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    $result = array('error' => 1, 'message' => __('Incorrect CAPTCHA'));
                    $controller->getResponse()->setBody($this->_coreData->jsonEncode($result));
                }
            }
        }
        return $this;
    }

    /**
     * Check Captcha On User Login Backend Page
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Captcha_Model_Observer
     */
    public function checkUserLoginBackend($observer)
    {
        $formId = 'backend_login';
        $captchaModel = $this->_helper->getCaptcha($formId);
        $login = $observer->getEvent()->getUsername();
        if ($captchaModel->isRequired($login)) {
            if (!$captchaModel->isCorrect($this->_getCaptchaString(Mage::app()->getRequest(), $formId))) {
                $captchaModel->logAttempt($login);
                throw new Magento_Backend_Model_Auth_Plugin_Exception(
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
     * @return Magento_Adminhtml_Model_Session
     */
    protected function _getBackendSession()
    {
        return Mage::getSingleton('Magento_Adminhtml_Model_Session');
    }

    /**
     * Check Captcha On User Login Backend Page
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Captcha_Model_Observer
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
                    $controller->setFlag('', Magento_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
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
     * @param Magento_Event_Observer $observer
     * @return Magento_Captcha_Model_Observer
     */
    public function resetAttemptForFrontend($observer)
    {
        return $this->_resetAttempt($observer->getModel()->getEmail());
    }

    /**
     * Reset Attempts For Backend
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Captcha_Model_Observer
     */
    public function resetAttemptForBackend($observer)
    {
        return $this->_resetAttempt($observer->getUser()->getUsername());
    }

    /**
     * Delete Unnecessary logged attempts
     *
     * @return Magento_Captcha_Model_Observer
     */
    public function deleteOldAttempts()
    {
        Mage::getResourceModel('Magento_Captcha_Model_Resource_Log')->deleteOldAttempts();
        return $this;
    }

    /**
     * Delete Expired Captcha Images
     *
     * @return Magento_Captcha_Model_Observer
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
     * @return Magento_Captcha_Model_Observer
     */
    protected function _resetAttempt($login)
    {
        Mage::getResourceModel('Magento_Captcha_Model_Resource_Log')->deleteUserAttempts($login);
        return $this;
    }

    /**
     * Get Captcha String
     *
     * @param Magento_Core_Controller_Request_Http $request
     * @param string $formId
     * @return string
     */
    protected function _getCaptchaString(Magento_Core_Controller_Request_Http $request, $formId)
    {
        $captchaParams = $request->getPost(Magento_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
        return isset($captchaParams[$formId]) ? $captchaParams[$formId] : '';
    }
}
