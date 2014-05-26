<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Catalog\Service\V1\Product\Link;

interface WriteServiceInterface
{
    /**
     * Assign a product link to another product
     *
     * @param string $productSku
     * @param \Magento\Catalog\Service\V1\Product\Link\Data\LinkedProductEntity[] $assignedProducts
     * @param string $type
     * @throws \Magento\Framework\Exception\Exception If new product links can not be saved
     * @throws \Magento\Framework\Exception\InputException If the product with provided SKU is missed
     * @return string[]
     */
    public function assign($productSku, array $assignedProducts, $type);
}
