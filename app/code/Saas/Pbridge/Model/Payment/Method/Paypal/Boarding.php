<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Pbridge_Model_Payment_Method_Paypal_Boarding extends Enterprise_Pbridge_Model_Payment_Method_Paypal
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code  = Saas_Paypal_Model_Boarding_Config::METHOD_DIRECT_BOARDING;

    /**
     * Website Payments Pro instance type
     *
     * @var $_proType string
     */
    protected $_proType = 'saas_pbridge/payment_method_paypal_boarding_pro';

    /**
     * Override enterprise PBridge method to fix PayPal Website Payments Pro with Permissions
     *
     * @param Mage_Sales_Model_Quote|null $quote
     * @return boolean
     */
    public function isAvailable($quote = null)
    {
        return $this->getPbridgeMethodInstance()->isDummyMethodAvailable($quote)
            && $this->_pro->getConfig()->isMethodAvailable(Saas_Paypal_Model_Boarding_Config::METHOD_DIRECT_BOARDING);
    }
}
