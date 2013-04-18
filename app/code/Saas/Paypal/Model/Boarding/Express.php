<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPal Express Checkout Permissions payment model
 */
class Saas_Paypal_Model_Boarding_Express extends Mage_Paypal_Model_Express
{
    /**
     * Payment method code
     * @var string
     */
    protected $_code  = Saas_Paypal_Model_Boarding_Config::METHOD_EXPRESS_BOARDING;

    /**
     * Form block for Express Permissions
     *
     * @var string
     */
    protected $_formBlockType = 'Saas_Paypal_Model_Boarding_Express_Form';

    /**
     * Website Payments Pro instance type
     *
     * @var $_proType string
     */
    protected $_proType = 'Saas_Paypal_Model_Boarding_Pro';

    /**
     * Checkout redirect URL getter for onepage checkout (hardcode)
     *
     * @see Mage_Checkout_OnepageController::savePaymentAction()
     * @see Mage_Sales_Model_Quote_Payment::getCheckoutRedirectUrl()
     * @return string
     */
    public function getCheckoutRedirectUrl()
    {
        return Mage::getUrl('paypal/boarding_express/start');
    }

    /**
     * Returns method's config object
     *
     * @return Mage_Paypal_Model_Config
     */
    public function getConfig()
    {
        return $this->_pro->getConfig();
    }
}
