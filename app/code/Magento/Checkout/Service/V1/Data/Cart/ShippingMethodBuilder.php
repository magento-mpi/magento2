<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data\Cart;

/**
 * Builder for the Shipping Method Data
 */
class ShippingMethodBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set carrier code
     *
     * @param string $value
     * @return $this
     */
    public function setCarrierCode($value)
    {
        return $this->_set(ShippingMethod::KEY_CARRIER_CODE, $value);
    }

    /**
     * Set shipping method code
     *
     * @param string $value
     * @return $this
     */
    public function setMethodCode($value)
    {
        return $this->_set(ShippingMethod::KEY_METHOD_CODE, $value);
    }

    /**
     * Set shipping description
     *
     * @param string $value
     * @return $this
     */
    public function setDescription($value)
    {
        return $this->_set(ShippingMethod::KEY_DESCRIPTION, $value);
    }

    /**
     * Set shipping amount
     *
     * @param float $value
     * @return $this
     */
    public function setAmount($value)
    {
        return $this->_set(ShippingMethod::KEY_SHIPPING_AMOUNT, $value);
    }
}
