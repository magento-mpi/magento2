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
 * Captcha Create User Observer
 *
 * @category    Mage
 * @package     Mage_Captcha
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Captcha_Model_Observer_CreateUser extends Mage_Captcha_Model_Observer_AbstractCustomer
{
    /**
     * @var string
     */
    protected $_formId = 'user_create';

    /**
     * Setup Redirect if Captcha Wrong
     *
     * @param Mage_Core_Controller_Varien_Action $controller
     */
    protected function _setupRedirect($controller)
    {
        $this->_getSession()->addError(Mage::helper('Mage_Captcha_Helper_Data')->__('Incorrect CAPTCHA.'));
        $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
        $this->_getSession()->setCustomerFormData($controller->getRequest()->getPost());
        $controller->getResponse()->setRedirect(Mage::getUrl('*/*/create'));
    }
}
