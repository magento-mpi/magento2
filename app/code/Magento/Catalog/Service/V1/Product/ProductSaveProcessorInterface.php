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
     * @param \Magento\Catalog\Service\V1\Data\Product $product
     * @return string id
     */
    public function create(\Magento\Catalog\Service\V1\Data\Product $product);

    /**
     * Update product.
     *
     * @param string $id
     * @param \Magento\Catalog\Service\V1\Data\Product $product
     * @return string id
     */
    public function update($id, \Magento\Catalog\Service\V1\Data\Product $product);

    /**
     * Delete product.
     *
     * @param \Magento\Catalog\Service\V1\Data\Product $product
     * @return bool
     */
    public function delete(\Magento\Catalog\Service\V1\Data\Product $product);
}
