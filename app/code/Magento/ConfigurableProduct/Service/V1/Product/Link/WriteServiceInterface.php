<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Product\Link;

interface WriteServiceInterface
{
    /**
     * @param  string $productSku
     * @param  string $childSku
     * @return bool
     */
    public function addChild($productSku, $childSku);
}
