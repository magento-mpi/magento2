<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1;

interface ReadServiceInterface
{
    /**
     * Generate variation based on same product
     *
     * @param \Magento\Catalog\Service\V1\Data\Product $product
     * @param \Magento\ConfigurableProduct\Service\V1\Data\Option[] $options
     * @return \Magento\Catalog\Service\V1\Data\Product[]
     */
    public function generateVariation(
        \Magento\Catalog\Service\V1\Data\Product $product,
        $options
    );
}
