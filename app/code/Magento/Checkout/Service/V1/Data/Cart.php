<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Data;

/**
 * Class Cart
 * @package Magento\Checkout\Service\V1\Data
 */
class Cart extends \Magento\Framework\Service\Data\Eav\AbstractObject
{
    CONST ID = 'id';

    CONST STORE_ID = 'store_id';

    CONST CREATED_AT = 'created_at';

    CONST UPDATED_AT = 'updated_at';

    CONST IS_ACTIVE = 'is_active';

    CONST ITEMS = 'items';

    CONST CUSTOMER = 'customer';

    CONST SHIPPING_ADDRESS = 'shipping_address';

    CONST BILLING_ADDRESS = 'shipping_address';

    CONST SUBTOTAL = 'subtotal';

    CONST GRAND_TOTAL = 'grand_total';

    public function getId()
    {
        return $this->_get(self::ID);
    }

    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    public function getUpdatedAt()
    {
        return $this->_get(self::UPDATED_AT);
    }

    public function getIsActive()
    {
        return $this->_get(self::IS_ACTIVE);
    }

    public function getItems()
    {
        return $this->_get(self::ITEMS);
    }

    public function getCustomer()
    {
        return $this->_get(self::CUSTOMER);
    }

    public function getShippingAddress()
    {
        return $this->_get(self::SHIPPING_ADDRESS);
    }

    public function getBillingAddress()
    {
        return $this->_get(self::BILLING_ADDRESS);
    }

    public function getSubtotal()
    {
        return $this->_get(self::SUBTOTAL);
    }

    public function getGrandTotal()
    {
        return $this->_get(self::GRAND_TOTAL);
    }
}