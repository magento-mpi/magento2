<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option;

/**
 * @deprecated
 * @see \Magento\Catalog\Api\Data\ProductCustomOptionAttributeInterface
 */
class Metadata extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    const PRICE = 'price';
    const PRICE_TYPE = 'price_type';
    const SKU = 'sku';
    const SORT_ORDER = 'sort_order';
    const FILE_EXTENSION = 'file_extension';
    const IMAGE_SIZE_X = 'image_size_x';
    const IMAGE_SIZE_Y = 'image_size_y';
    const MAX_CHARACTERS = 'max_characters';
    const TITLE = 'title';
    const OPTION_TYPE_ID = 'option_type_id';

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
     * Get price type
     *
     * @return string
     */
    public function getPriceType()
    {
        return $this->_get(self::PRICE_TYPE);
    }

    /**
     * Get Sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->_get(self::SKU);
    }
}
