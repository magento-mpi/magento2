<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Google Checkout shipping model
 *
 * @category   Magento
 * @package    Magento_GoogleCheckout
 */
class Magento_GoogleCheckout_Model_Shipping extends Magento_Shipping_Model_Carrier_Abstract
{
    protected $_code = 'googlecheckout';

    /**
     * Collects rates for user request
     *
     * @param Magento_Shipping_Model_Rate_Request $data
     * @return Magento_Shipping_Model_Rate_Result
     */
    public function collectRates(Magento_Shipping_Model_Rate_Request $request)
    {
        // dummy placeholder
        return $this;
    }

    /**
     * Returns array(methodCode => methodName) of possible methods for this carrier
     * Used to automatically show it in config and so on
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return array();
    }

    /**
     * Returns array(methodCode => methodName) of internally used methods.
     * They are possible only as result of completing Google Checkout.
     *
     * @return array
     */
    public function getInternallyAllowedMethods()
    {
        return array(
            'carrier'  => 'Carrier',
            'merchant' => 'Merchant',
            'flatrate' => 'Flat Rate',
            'pickup'   => 'Pickup'
        );
    }
}
