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
 * Captcha Abstract Observer
 *
 * @category    Mage
 * @package     Mage_Captcha
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Captcha_Model_Observer_AbstractCustomer
{
    /**
     * @var string
     */
    protected $_formId;

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
            if (!$captchaModel->isCorrect($this->_getCaptchaString($observer->getControllerAction()->getRequest()))) {
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
    abstract protected function _setupRedirect($controller);

    /**
     * Get Captcha String
     *
     * @param Varien_Object $request
     * @return string
     */
    protected function _getCaptchaString($request)
    {
        $captchaParams = $request->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
        return $captchaParams[$this->_formId];
    }

    /**
     * Get Session
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Mage_Customer_Model_Session');
    }
}
