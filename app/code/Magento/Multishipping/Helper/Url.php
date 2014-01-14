<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multi Shipping urls helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Multishipping\Helper;

class Url extends \Magento\Core\Helper\Url
{
    /**
     * Retrieve checkout url
     *
     * @return string
     */
    public function getMSCheckoutUrl()
    {
        return $this->_getUrl('multishipping/checkout');
    }

    /**
     * Retrieve login url
     *
     * @return string
     */
    public function getMSLoginUrl()
    {
        return $this->_getUrl('multishipping/checkout/login', array('_secure' => true, '_current' => true));
    }

    /**
     * Retrieve address url
     *
     * @return string
     */
    public function getMSAddressesUrl()
    {
        return $this->_getUrl('multishipping/checkout/addresses');
    }

    /**
     * Retrieve shipping address save url
     *
     * @return string
     */
    public function getMSShippingAddressSavedUrl()
    {
        return $this->_getUrl('multishipping/checkout_address/shippingSaved');
    }

    /**
     * Retrieve register url
     *
     * @return string
     */
    public function getMSRegisterUrl()
    {
        return $this->_getUrl('multishipping/checkout/register');
    }
}
