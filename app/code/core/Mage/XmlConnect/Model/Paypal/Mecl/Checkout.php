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
 * XmlConnect PayPal Mobile Express Checkout Library model
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Paypal_Mecl_Checkout extends Mage_Paypal_Model_Express_Checkout
{
    /**
     * Payment method type
     *
     * @var string
     */
    protected $_methodType = Mage_XmlConnect_Model_Payment_Method_Paypal_Config::METHOD_WPP_MECL;
}
