<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * PayPal online logo with additional options
 */
class Saas_Paypal_Block_Logo extends Mage_Paypal_Block_Logo
{
    /**
     * Return ALT for Paypal Landing page
     *
     * @return string
     */
    public function getAlt()
    {
        /** @var $paypalHelper Mage_Paypal_Helper_Data */
        $paypalHelper = Mage::helper('Mage_Paypal_Helper_Data');
        if ($this->_getConfig()->getMerchantCountry() == Saas_Paypal_Model_Config::LOCALE_DE) {
            return $paypalHelper->__('PayPal empfohlen');
        }
        return $paypalHelper->__('Additional Options');
    }
}
