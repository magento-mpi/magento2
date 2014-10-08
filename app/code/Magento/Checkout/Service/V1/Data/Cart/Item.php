<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Data\Cart;

/**
 * Shopping Cart item data object.
 * 
 * @codeCoverageIgnore
 */
class Item extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    /**
     * Item ID.
     */
    const ITEM_ID = 'item_id';
    
    /**
     * Product SKU.
     */
    const SKU = 'sku';

    /**
     * Product quantity.
     */
    const QTY = 'qty';

    /**
     * Product price.
     */
    const NAME = 'name';

    /**
     * Product name.
     */
    const PRICE = 'price';

    /**
     * Product type.
     */
    const PRODUCT_TYPE = 'product_type';

    /**
     * Returns the item ID.
     *
     * @return int|null Item ID. Otherwise, null.
     */
    public function getItemId()
    {
        return $this->_get(self::ITEM_ID);
    }

    /**
     * Returns the product SKU.
     *
     * @return string|null Product SKU. Otherwise, null.
     */
    public function getSku()
    {
        return $this->_get(self::SKU);
    }

    /**
     * Returns the product quantity.
     *
     * @return int Product quantity.
     */
    public function getQty()
    {
        return $this->_get(self::QTY);
    }

    /**
     * Returns the product name.
     *
     * @return string|null Product name. Otherwise, null.
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Returns the product price.
     *
     * @return float|null Product price. Otherwise, null.
     */
    public function getPrice()
    {
        return $this->_get(self::PRICE);
    }

    /**
     * Returns the product type.
     *
     * @return string|null Product type. Otherwise, null.
     */
    public function getProductType()
    {
        return $this->_get(self::PRODUCT_TYPE);
    }
}
