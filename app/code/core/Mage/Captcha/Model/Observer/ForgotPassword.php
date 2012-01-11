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
 * Captcha Forgot Password Observer
 *
 * @category    Mage
 * @package     Mage_Captcha
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Captcha_Model_Observer_ForgotPassword extends Mage_Captcha_Model_Observer_AbstractCustomer
{
    /**
     * @var string
     */
    protected $_formId = 'user_forgotpassword';

    /**
     * Setup Redirect if Captcha Wrong
     *
     * @param Mage_Core_Controller_Varien_Action $controller
     */
    protected function _setupRedirect($controller)
    {
        $this->_getSession()->addError(Mage::helper('Mage_Captcha_Helper_Data')->__('Incorrect CAPTCHA.'));
        $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
        $email = (string) $controller->getRequest()->getPost('email');
        $this->_getSession()->setEmail($email);
        $controller->getResponse()->setRedirect(Mage::getUrl('*/*/forgotpassword'));
    }
}
