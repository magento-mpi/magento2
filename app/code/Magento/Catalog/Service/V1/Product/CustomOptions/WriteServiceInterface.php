<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Catalog\Service\V1\Product\CustomOptions;

interface WriteServiceInterface
{
    /**
     * Add custom option to the product
     *
     * @param string $productSku
     * @param \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option $option
     * @return int
     */
    public function add($productSku, \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option $option);
}
