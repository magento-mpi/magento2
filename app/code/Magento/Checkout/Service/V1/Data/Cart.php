<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data;

/**
 * Cart data object
 *
 * @codeCoverageIgnore
 */
class Cart extends \Magento\Framework\Service\Data\AbstractObject
{
    CONST ID = 'id';

    CONST STORE_ID = 'store_id';

    CONST CREATED_AT = 'created_at';

    CONST UPDATED_AT = 'updated_at';

    CONST CONVERTED_AT = 'converted_at';

    CONST IS_ACTIVE = 'is_active';

    CONST IS_VIRTUAL = 'is_virtual';

    CONST ITEMS = 'items';

    CONST ITEMS_COUNT = 'items_count';

    CONST ITEMS_QUANTITY = 'items_qty';

    CONST CUSTOMER = 'customer';

    CONST CHECKOUT_METHOD = 'checkout_method';

    CONST SHIPPING_ADDRESS = 'shipping_address';

    CONST BILLING_ADDRESS = 'shipping_address';

    CONST TOTALS = 'totals';

    CONST RESERVED_ORDER_ID = 'reserved_order_id';

    CONST ORIG_ORDER_ID = 'orig_order_id';

    /**
     * Cart/Quote id
     *
     * @return int
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * Creation date and time
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * Last update date and time
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->_get(self::UPDATED_AT);
    }

    /**
     * Get conversion date and time
     *
     * @return string|null
     */
    public function getConvertedAt()
    {
        return $this->_get(self::CONVERTED_AT);
    }

    /**
     * Get active status flag
     *
     * @return bool|null
     */
    public function getIsActive()
    {
        $value = $this->_get(self::IS_ACTIVE);
        if (!is_null($value)) {
            $value = (bool)$value;
        }

        return $value;
    }

    /**
     * Get virtual flag(cart contains virtual products)
     *
     * @return bool|null
     */
    public function getIsVirtual()
    {
        $value = $this->_get(self::IS_VIRTUAL);
        if (!is_null($value)) {
            $value = (bool)$value;
        }

        return $value;
    }

    /**
     * Get cart items
     *
     * @return \Magento\Checkout\Service\V1\Data\Cart\Item[]|null
     */
    public function getItems()
    {
        return $this->_get(self::ITEMS);
    }

    /**
     * Amount of different items/products in cart
     *
     * @return int|null
     */
    public function getItemsCount()
    {
        return $this->_get(self::ITEMS_COUNT);
    }

    /**
     * Get quantity of all items/products in cart
     *
     * @return double|null
     */
    public function getItemsQuantity()
    {
        return $this->_get(self::ITEMS_QUANTITY);
    }

    /**
     * Get customer data
     *
     * @return \Magento\Checkout\Service\V1\Data\Cart\Customer
     */
    public function getCustomer()
    {
        return $this->_get(self::CUSTOMER);
    }

    /**
     * Get checkout method
     *
     * @return string|null
     */
    public function getCheckoutMethod()
    {
        return $this->_get(self::CHECKOUT_METHOD);
    }

    /**
     * @return \Magento\Checkout\Service\V1\Data\Cart\Address|null
     */
    public function getShippingAddress()
    {
        return $this->_get(self::SHIPPING_ADDRESS);
    }

    /**
     * @return \Magento\Checkout\Service\V1\Data\Cart\Address|null
     */
    public function getBillingAddress()
    {
        return $this->_get(self::BILLING_ADDRESS);
    }

    /**
     * @return \Magento\Checkout\Service\V1\Data\Cart\Totals|null
     */
    public function getTotals()
    {
        return $this->_get(self::TOTALS);
    }

    /**
     * Get reserved order id
     *
     * @return string|null
     */
    public function getReservedOrderId()
    {
        return $this->_get(self::RESERVED_ORDER_ID);
    }

    /**
     * Get original order id
     *
     * @return string|null
     */
    public function getOrigOrderId()
    {
        return $this->_get(self::ORIG_ORDER_ID);
    }
}

