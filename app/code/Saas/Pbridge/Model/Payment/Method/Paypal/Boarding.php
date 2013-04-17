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
    //TODO should it be replaced to constant???
    protected $_code  = 'paypal_direct_boarding';// Saas_Paypal_Model_Boarding_Config::METHOD_DIRECT_BOARDING;

    /**
     * Website Payments Pro instance type
     *
     * @var $_proType string
     */
    protected $_proType = 'Saas_Pbridge_Model_Payment_Method_Paypal_Boarding_Pro';

    /**
     * Override enterprise PBridge method to fix PayPal Website Payments Pro with Permissions
     *
     * @param Mage_Sales_Model_Quote|null $quote
     * @return boolean
     */
    public function isAvailable($quote = null)
    {
        return $this->getPbridgeMethodInstance()->isDummyMethodAvailable($quote)
            && $this->_pro->getConfig()->isMethodAvailable($this->_code);
        //TODO Saas_Paypal_Model_Boarding_Config::METHOD_DIRECT_BOARDING was replaced to $this->_code
    }
}
