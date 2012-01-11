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
 * Captcha User Login Backend Observer
 *
 * @category    Mage
 * @package     Mage_Captcha
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Captcha_Model_Observer_UserLoginBackend extends Mage_Captcha_Model_Observer_AbstractCustomer
{
    /**
     * @var string
     */
    protected $_formId = 'backend_login';

    /**
     * Check Captcha
     *
     * @param Varien_Object $observer
     * @return Mage_Bundle_Model_Observer
     */
    public function checkCaptcha($observer)
    {
        $captchaModel = Mage::helper('Mage_Captcha_Helper_Data')->getCaptcha($this->_formId);
        if ($captchaModel->isRequired()){
            if (!$captchaModel->isCorrect($this->_getCaptchaString(Mage::app()->getRequest()))) {
                $this->_setupRedirect($observer->getControllerAction());
            }
        }
        Mage::helper('Mage_Captcha_Helper_Data')->getCaptcha($this->_formId)->logAttempt();
        return $this;
    }

    /**
     * Setup Redirect if Captcha Wrong
     *
     * @param Mage_Core_Controller_Varien_Action $controller
     */
    protected function _setupRedirect($controller)
    {
        Mage::helper('Mage_Captcha_Helper_Data')->getCaptcha($this->_formId)->logAttempt();
        Mage::throwException(Mage::helper('Mage_Captcha_Helper_Data')->__('Incorrect CAPTCHA.'));
    }
}
