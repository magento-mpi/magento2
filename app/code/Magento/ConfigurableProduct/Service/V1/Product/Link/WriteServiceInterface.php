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
     * Remove configurable product option
     *
     * @param string $productSku
     * @param string $childSku
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Webapi\Exception
     * @return bool
     */
    public function removeChild($productSku, $childSku);
}
