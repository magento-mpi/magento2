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
namespace Magento\GoogleCheckout\Model;

class Shipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier
{
    protected $_code = 'googlecheckout';

    /**
     * Collects rates for user request
     *
     * @param \Magento\Shipping\Model\Rate\Request $data
     * @return \Magento\Shipping\Model\Rate\Result
     */
    public function collectRates(\Magento\Shipping\Model\Rate\Request $request)
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
