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
    protected $_customerData;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData;

    /**
     * @var Magento_Checkout_Model_Type_Onepage
     */
    protected $_typeOnepage;

    /**
     * @var Magento_Core_Model_Session_Abstract
     */
    protected $_session;

    /**
     * @var Magento_Captcha_Model_Resource_LogFactory
     */
    protected $_resLogFactory;

    /**
     * @param Magento_Captcha_Model_Resource_LogFactory $resLogFactory
     * @param Magento_Core_Model_Session_Abstract $session
     * @param Magento_Checkout_Model_Type_Onepage $typeOnepage
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Customer_Helper_Data $customerData
     * @param Magento_Captcha_Helper_Data $helper
     * @param Magento_Core_Model_Url $urlManager
     * @param Magento_Filesystem $filesystem
     */
    public function __construct(
        Magento_Captcha_Model_Resource_LogFactory $resLogFactory,
        Magento_Core_Model_Session_Abstract $session,
        Magento_Checkout_Model_Type_Onepage $typeOnepage,
        Magento_Core_Helper_Data $coreData,
        Magento_Customer_Helper_Data $customerData,
        Magento_Captcha_Helper_Data $helper,
        Magento_Core_Model_Url $urlManager,
        Magento_Filesystem $filesystem
    ) {
        $this->_resLogFactory = $resLogFactory;
        $this->_session = $session;
        $this->_typeOnepage = $typeOnepage;
        $this->_coreData = $coreData;
        $this->_customerData = $customerData;
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
                $this->_session->addError(__('Incorrect CAPTCHA'));
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
                $this->_session->addError(__('Incorrect CAPTCHA.'));
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
                $this->_session->addError(__('Incorrect CAPTCHA'));
                $controller->setFlag('', Magento_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                $this->_session->setUsername($login);
                $beforeUrl = $this->_session->getBeforeAuthUrl();
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
                $this->_session->addError(__('Incorrect CAPTCHA'));
                $controller->setFlag('', Magento_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                $this->_session->setCustomerFormData($controller->getRequest()->getPost());
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
        $checkoutMethod = $this->_typeOnepage->getQuote()->getCheckoutMethod();
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
        $checkoutMethod = $this->_typeOnepage->getQuote()->getCheckoutMethod();
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
     * @throws Magento_Backend_Model_Auth_Plugin_Exception
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

        if (!empty($email) && !empty($params)) {
            if ($captchaModel->isRequired()) {
                if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                    $this->_session->setEmail((string) $controller->getRequest()->getPost('email'));
                    $controller->setFlag('', Magento_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    $this->_session->addError(__('Incorrect CAPTCHA'));
                    $controller->getResponse()
                        ->setRedirect($controller->getUrl('*/*/forgotpassword', array('_nosecret' => true)));
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
        return $this->_getResourceModel()->deleteUserAttempts(
            $observer->getModel()->getEmail()
        );
    }

    /**
     * Reset Attempts For Backend
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Captcha_Model_Observer
     */
    public function resetAttemptForBackend($observer)
    {
        return $this->_getResourceModel()->deleteUserAttempts(
            $observer->getUser()->getUsername()
        );
    }

    /**
     * Delete Unnecessary logged attempts
     *
     * @return Magento_Captcha_Model_Observer
     */
    public function deleteOldAttempts()
    {
        $this->_getResourceModel()->deleteOldAttempts();
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

    /**
     * Get resource model
     *
     * @return Magento_Captcha_Model_Resource_Log
     */
    protected function _getResourceModel()
    {
        return $this->_resLogFactory->create();
    }
}
