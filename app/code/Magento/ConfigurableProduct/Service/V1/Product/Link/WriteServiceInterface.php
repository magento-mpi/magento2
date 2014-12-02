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
     * @see \Magento\ConfigurableProduct\Api\LinkManagementInterface::addChild
     */
    public function addChild($productSku, $childSku);

    /**
     * Remove configurable product option
     *
     * @param string $productSku
     * @param string $childSku
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Webapi\Exception
     * @return bool
     * @see \Magento\ConfigurableProduct\Api\LinkManagementInterface::removeChild
     */
    public function removeChild($productSku, $childSku);
}
