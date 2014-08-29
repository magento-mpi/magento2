<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product;

/**
 * Interface for product data modification.
 */
interface ProductSaveProcessorInterface
{
    /**
     * Create product.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Service\V1\Data\Product $productData
     * @return string id
     */
    public function create(
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Service\V1\Data\Product $productData
    );

    /**
     * Create product after the initial creation is complete.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Service\V1\Data\Product $productData
     * @return string id
     */
    public function afterCreate(
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Service\V1\Data\Product $productData
    );

    /**
     * Update product.
     *
     * @param string $sku
     * @param \Magento\Catalog\Service\V1\Data\Product $product
     * @return string id
     */
    public function update($sku, \Magento\Catalog\Service\V1\Data\Product $product);

    /**
     * Delete product.
     *
     * @param \Magento\Catalog\Service\V1\Data\Product $product
     * @return void
     */
    public function delete(\Magento\Catalog\Service\V1\Data\Product $product);
}
