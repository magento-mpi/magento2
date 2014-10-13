<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link\Data;

/**
 * ProductLink Service Data Object
 *
 * @codeCoverageIgnore
 * @deprecated
 * @see \Magento\Catalog\Api\Data\ProductLinkInterface
 */
class ProductLink extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    /**#@+
     * Constants for Data Object keys
     */
    const TYPE = 'type';
    const ATTRIBUTE_SET_ID = 'attribute_set_id';
    const SKU = 'sku';
    const POSITION = 'position';

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }

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
     * Get product position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }
}
