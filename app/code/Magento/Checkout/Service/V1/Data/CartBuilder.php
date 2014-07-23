<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data;

/**
 * Cart data object builder
 *
 * @codeCoverageIgnore
 */
class CartBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Cart/quote id
     *
     * @param int $value
     * @return $this
     */
    public function setId($value)
    {
        return $this->_set(Cart::ID, $value);
    }

    /**
     * Store id
     *
     * @param int $value
     * @return $this
     */
    public function setStoreId($value)
    {
        return $this->_set(Cart::STORE_ID, $value);
    }

    /**
     * set creation date and time
     *
     * @param $value
     * @return $this
     */
    public function setCreatedAt($value)
    {
        return $this->_set(Cart::CREATED_AT, $value);
    }

    /**
     * Set last update date and time
     *
     * @param string $value
     * @return $this
     */
    public function setUpdatedAt($value)
    {
        return $this->_set(Cart::UPDATED_AT, $value);
    }

    /**
     * Set convertion date and time
     *
     * @param string $value
     * @return $this
     */
    public function setConvertedAt($value)
    {
        return $this->_set(Cart::CONVERTED_AT, $value);
    }

    /**
     * Set active status
     *
     * @param mixed|null $value
     * @return $this
     */
    public function setIsActive($value)
    {
        return $this->_set(Cart::IS_ACTIVE, $value);
    }

    /**
     * Set items count(amount of different products)
     *
     * @param int $value
     * @return $this
     */
    public function setItemsCount($value)
    {
        return $this->_set(Cart::ITEMS_COUNT, $value);
    }

    /**
     * Set items quantity(total amount of all products)
     *
     * @param double $value
     * @return $this
     */
    public function setItemsQuantity($value)
    {
        return $this->_set(Cart::ITEMS_QUANTITY, $value);
    }

    /**
     * Set customer data object
     *
     * @param $value
     * @return $this
     */
    public function setCustomer($value)
    {
        return $this->_set(Cart::CUSTOMER, $value);
    }

    /**
     * Set checkout method
     *
     * @param string $value
     * @return $this
     */
    public function setCheckoutMethod($value)
    {
        return $this->_set(Cart::CHECKOUT_METHOD, $value);
    }

    /**
     * Set shipping address data object
     *
     * @param $value
     * @return $this
     */
    public function setShippingAddress($value)
    {
        return $this->_set(Cart::SHIPPING_ADDRESS, $value);
    }

    /**
     * Set billing address data object
     *
     * @param $value
     * @return $this
     */
    public function setBillingAddress($value)
    {
        return $this->_set(Cart::BILLING_ADDRESS, $value);
    }

    /**
     * @param $value \Magento\Checkout\Service\V1\Data\Cart\Totals
     * @return $this
     */
    public function setTotals($value)
    {
        return $this->_set(Cart::TOTALS, $value);
    }

    /**
     * Set reserved order id
     *
     * @param $value string
     * @return $this
     */
    public function setReservedOrderId($value)
    {
        return $this->_set(self::RESERVED_ORDER_ID,  $value);
    }

    /**
     * Set original order id
     *
     * @param $value string
     * @return $this
     */
    public function setOrigOrderId($value)
    {
        return $this->_set(self::ORIG_ORDER_ID, $value);
    }
}
