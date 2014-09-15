<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Service\V1\Data;

class PackagesItems extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    /**#@+
     * Constants defined for keys of array
     */
    const QTY = 'qty';
    const CUSTOMS_VALUE = 'customs_value';
    const PRICE = 'price';
    const NAME = 'name';
    const WEIGHT = 'weight';
    const PRODUCT_ID = 'product_id';
    const ORDER_ITEM_ID = 'order_item_id';
    /**#@-*/

    /**
     * Get qty
     *
     * @return int
     */
    public function getQty()
    {
        return $this->_get(self::QTY);
    }

    /**
     * Get customs_value
     *
     * @return string
     */
    public function getCustomsValue()
    {
        return $this->_get(self::CUSTOMS_VALUE);
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->_get(self::PRICE);
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Get weight
     *
     * @return float
     */
    public function getWeight()
    {
        return $this->_get(self::WEIGHT);
    }

    /**
     * Get product_id
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->_get(self::PRODUCT_ID);
    }

    /**
     * Get order_item_id
     *
     * @return int
     */
    public function getOrderItemId()
    {
        return $this->_get(self::ORDER_ITEM_ID);
    }
}
