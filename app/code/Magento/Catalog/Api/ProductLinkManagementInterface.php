<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api;

interface ProductLinkManagementInterface
{
    /**
     * Provide the list of links for a specific product
     *
     * @param string $productSku
     * @param string $type
     * @return Data\ProductLinkInterface[]
     */
    public function getList($productSku, $type);

    /**
     * Assign a product link to another product
     *
     * @param string $productSku
     * @param string $linkType
     * @param \Magento\Catalog\Api\Data\ProductLinkInterface[] $items
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     * @see \Magento\Catalog\Service\V1\Product\Link\WriteServiceInterface::assign - previous implementation
     */
    public function assign($productSku, $linkType, array $items);

    /**
     * Update product link
     *
     * @param string $productSku
     * @param string $linkType
     * @param \Magento\Catalog\Api\Data\ProductLinkInterface $linkedProduct
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     * @see \Magento\Catalog\Service\V1\Product\Link\WriteServiceInterface::update - prevuois implementation
     */
    public function update($productSku, $linkType, \Magento\Catalog\Api\Data\ProductLinkInterface $linkedProduct);

    /**
     * Remove the product link from a specific product
     *
     * @param string $productSku
     * @param string $linkType
     * @param string $linkedProductSku
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     *
     * @see \Magento\Catalog\Service\V1\Product\Link\WriteServiceInterface::remove - prevuois implementation
     */
    public function remove($productSku, $linkType, $linkedProductSku);
}
