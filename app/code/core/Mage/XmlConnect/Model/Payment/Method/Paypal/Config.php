<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPal Mobile Express Checkout Library config
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Payment_Method_Paypal_Config extends Mage_Paypal_Model_Config
{
    /**
     * PayPal Website Payments Pro - PayPal Mobile Express Checkout Library
     */
    const METHOD_WPP_MECL = 'paypal_mecl';

    /**
     * Get url for dispatching customer to express checkout start
     *
     * @param string $token
     * @return string
     */
    public function getExpressCheckoutStartUrl($token)
    {
        return $this->getPaypalUrl(array('cmd' => '_express-checkout-mobile', 'token' => $token));
    }
}
