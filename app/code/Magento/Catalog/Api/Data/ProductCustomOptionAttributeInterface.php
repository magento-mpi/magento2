<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data;

/**
 * Created from:
 * @see \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata
 * @todo Create \Magento\Catalog\Model\Product\CustomOption\Metadata and extend abstract framework implementation
 */
interface ProductCustomOptionAttributeInterface
{
    /**
     * Get price
     *
     * @return float
     */
    public function getPrice();

    /**
     * Get price type
     *
     * @return string
     */
    public function getPriceType();

    /**
     * Get Sku
     *
     * @return string
     */
    public function getSku();
}
