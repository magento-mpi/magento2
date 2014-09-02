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
class ShippingMethodBuilder extends \Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder
{
    /**
     * Set carrier code
     *
     * @param string $value
     * @return $this
     */
    public function setCarrierCode($value)
    {
        return $this->_set(ShippingMethod::CARRIER_CODE, $value);
    }

    /**
     * Set shipping method code
     *
     * @param string $value
     * @return $this
     */
    public function setMethodCode($value)
    {
        return $this->_set(ShippingMethod::METHOD_CODE, $value);
    }

    /**
     * Set shipping carrier title
     *
     * @param string $value
     * @return $this
     */
    public function setCarrierTitle($value)
    {
        return $this->_set(ShippingMethod::CARRIER_TITLE, $value);
    }

    /**
     * Set shipping method title
     *
     * @param string $value
     * @return $this
     */
    public function setMethodTitle($value)
    {
        return $this->_set(ShippingMethod::METHOD_TITLE, $value);
    }

    /**
     * Set shipping amount
     *
     * @param float $value
     * @return $this
     */
    public function setAmount($value)
    {
        return $this->_set(ShippingMethod::SHIPPING_AMOUNT, $value);
    }

    /**
     * Set base shipping amount
     *
     * @param float $value
     * @return $this
     */
    public function setBaseAmount($value)
    {
        return $this->_set(ShippingMethod::BASE_SHIPPING_AMOUNT, $value);
    }

    /**
     * Set method availability flag
     *
     * @param bool $value
     * @return $this
     */
    public function setAvailable($value)
    {
        return $this->_set(ShippingMethod::AVAILABLE, (bool)$value);
    }
}
