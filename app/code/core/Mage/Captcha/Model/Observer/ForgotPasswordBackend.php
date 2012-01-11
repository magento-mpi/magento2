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
 * Captcha Forgot Password Backend Observer
 *
 * @category    Mage
 * @package     Mage_Captcha
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Captcha_Model_Observer_ForgotPasswordBackend extends Mage_Captcha_Model_Observer_AbstractCustomer
{
    /**
     * @var string
     */
    protected $_formId = 'backend_forgotpassword';

    /**
     * Check Captcha
     *
     * @param Varien_Object $observer
     * @return Mage_Bundle_Model_Observer
     */
    public function checkCaptcha($observer)
    {
        $email = (string) $observer->getControllerAction()->getRequest()->getParam('email');
        $params = $observer->getControllerAction()->getRequest()->getParams();

        if (!empty($email) && !empty($params)) {
            parent::checkCaptcha($observer);
        }

        return $this;
    }

    /**
     * Setup Redirect if Captcha Wrong
     *
     * @param Mage_Core_Controller_Varien_Action $controller
     */
    protected function _setupRedirect($controller)
    {
        $this->_getSession()->setEmail((string) $controller->getRequest()->getPost('email'));
        $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
        $this->_getSession()->addError(Mage::helper('Mage_Captcha_Helper_Data')->__('Incorrect CAPTCHA.'));
        $controller->getResponse()->setRedirect(Mage::getUrl('*/*/forgotpassword'));
    }

    /**
     * Get Session
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Mage_Adminhtml_Model_Session');
    }
}
