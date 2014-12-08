<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Product\Link;

interface ReadServiceInterface
{
    /**
     * Get all children for Bundle product
     *
     * @param string $productId
     * @return \Magento\Catalog\Service\V1\Data\Product[]
     */
    public function getChildren($productId);
}
