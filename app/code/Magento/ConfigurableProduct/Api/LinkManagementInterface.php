<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Api;

interface LinkManagementInterface
{
    /**
     * Get all children for Bundle product
     *
     * @param string $productId
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     * @see \Magento\ConfigurableProduct\Service\V1\Product\Link\ReadServiceInterface::getChildren
     */
    public function getChildren($productId);

    /**
     * @param  string $productSku
     * @param  string $childSku
     * @return bool
     * @see \Magento\ConfigurableProduct\Service\V1\Product\Link\WriteServiceInterface::addChild
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
     * @see \Magento\ConfigurableProduct\Service\V1\Product\Link\WriteServiceInterface::removeChild
     */
    public function removeChild($productSku, $childSku);
}
