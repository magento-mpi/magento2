<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data;

class CartBuilder extends \Magento\Framework\Service\Data\Eav\AbstractObjectBuilder
{
    public function setId($value)
    {
        return $this->_set(Cart::ID, $value);
    }

    public function setStoreId($value)
    {
        return $this->_set(Cart::STORE_ID, $value);
    }

    public function setCreatedAt($value)
    {
        return $this->_set(Cart::CREATED_AT, $value);
    }

    public function setUpdatedAt($value)
    {
        return $this->_set(Cart::UPDATED_AT, $value);
    }

    public function setIsActive($value)
    {
        return $this->_set(Cart::IS_ACTIVE, $value);
    }

    public function setItems($value)
    {
        return $this->_set(Cart::ITEMS, $value);
    }

    public function setCustomer($value)
    {
        return $this->_set(Cart::CUSTOMER, $value);
    }

    public function setShippingAddress($value)
    {
        return $this->_set(Cart::SHIPPING_ADDRESS, $value);
    }

    public function setBillingAddress($value)
    {
        return $this->_set(Cart::BILLING_ADDRESS, $value);
    }

    public function setSubtotal($value)
    {
        return $this->_set(Cart::SUBTOTAL, $value);
    }

    public function setGrandTotal($value)
    {
        return $this->_set(Cart::GRAND_TOTAL, $value);
    }
}