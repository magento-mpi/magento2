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
 * Interface for product data loading.
 */
interface ProductLoadProcessorInterface
{
    /**
     * Load product data to the builder, which can be used to instantiate product object.
     *
     * @param string $id
     * @param \Magento\Catalog\Service\V1\Data\ProductBuilder $productBuilder
     * @return void
     */
    public function load($id, \Magento\Catalog\Service\V1\Data\ProductBuilder $productBuilder);
}
