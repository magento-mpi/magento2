<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PaypalUk
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wrapper that performs Paypal Express and Checkout communication
 * Use current Paypal Express method instance
 */
class Magento_PaypalUk_Model_Express_Checkout extends Magento_Paypal_Model_Express_Checkout
{
    /**
     * Api Model Type
     *
     * @var string
     */
    protected $_apiType = 'Magento_PaypalUk_Model_Api_Nvp';

    /**
     * Payment method type
     *
     * @var string
     */
    protected $_methodType = Magento_Paypal_Model_Config::METHOD_WPP_PE_EXPRESS;

    /**
     * Set shipping method to quote, if needed
     * @param string $methodCode
     */
    public function updateShippingMethod($methodCode)
    {
        parent::updateShippingMethod($methodCode);
        $this->_quote->save();
    }
}
