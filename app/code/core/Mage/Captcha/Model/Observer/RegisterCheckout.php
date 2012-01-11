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
 * Captcha Register During Checkout Observer
 *
 * @category    Mage
 * @package     Mage_Captcha
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Captcha_Model_Observer_RegisterCheckout extends Mage_Captcha_Model_Observer_AbstractCustomer
{
    /**
     * @var string
     */
    protected $_formId = 'register_during_checkout';

    /**
     * Check Captcha
     *
     * @param Varien_Object $observer
     * @return Mage_Bundle_Model_Observer
     */
    public function checkCaptcha($observer)
    {
        $quote = Mage::getSingleton('Mage_Checkout_Model_Type_Onepage')->getQuote();

        if ($quote->getCheckoutMethod() == Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER){
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
        $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
        $result = array('error' => 1, 'message' => Mage::helper('Mage_Captcha_Helper_Data')->__('Incorrect CAPTCHA.'));
        $controller->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode($result));
    }
}
