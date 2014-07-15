<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Service\V1\Product\Link\Data;

/**
 * Bundle ProductLink Service Data Object
 */
class ProductLink extends \Magento\Framework\Service\Data\Eav\AbstractObject
{
    /**
     * Constants for Data Object keys
     */
    const OPTION_ID = 'option_id';
    const PARENT_SKU = 'parent_product_sku';
    const SKU = 'product_sku';
    const POSITION = 'position';
    const IS_DEFAULT = 'is_default';
    const PRICE_TYPE = 'slection_price_type';
    const PRICE_VALUE = 'slection_price_value';
    const QUANTITY = 'selection_qty';
    const CAN_CHANGE_QUANTITY = 'selection_can_change_qty';

    /**
     * Get product sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->_get(self::SKU);
    }

    /**
     * Get option ID
     *
     * @return int
     */
    public function getOptionId()
    {
        return $this->_get(self::OPTION_ID);
    }

    /**
     * Get parent product SKU
     *
     * @return string
     */
    public function getParentSku()
    {
        return $this->_get(self::PARENT_SKU);
    }

    /**
     * Get product position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }

    /**
     * @return boolean
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsDefault()
    {
        return $this->_get(self::IS_DEFAULT);
    }

    /**
     * Get price type
     *
     * @return int
     */
    public function getPriceType()
    {
        return $this->_get(self::PRICE_TYPE);
    }

    /**
     * Get price value
     *
     * @return float
     */
    public function getPriceValue()
    {
        return $this->_get(self::PRICE_VALUE);
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->_get(self::QUANTITY);
    }

    /**
     * Get whether quantity could be changed
     *
     * @return int
     */
    public function getCanChangeQuantity()
    {
        return $this->_get(self::CAN_CHANGE_QUANTITY);
    }
}
