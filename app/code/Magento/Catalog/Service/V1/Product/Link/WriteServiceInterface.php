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
     * Assign a product link to another product.
     * @param int $linkType
     * @param int $productId
     * @param Data\LinkedProductEntity[] $assignedProducts
     * @return mixed
     */
    public function assign($productId, array $assignedProducts, $linkType);
}
