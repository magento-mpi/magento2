<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product price interface for external catalogs
 */
namespace Magento\Catalog\Model\Product;

interface CatalogPriceInterface
{
    /**
     * Minimal price for "regular" user
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param null|\Magento\Core\Model\Store $store Store view
     * @param bool $inclTax
     * @return null|float
     */
    public function getCatalogPrice(\Magento\Catalog\Model\Product $product, $store = null, $inclTax = false);

    /**
     * Calculate price without discount for external catalogs if applicable
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     */
    public function getCatalogRegularPrice(\Magento\Catalog\Model\Product $product);
}