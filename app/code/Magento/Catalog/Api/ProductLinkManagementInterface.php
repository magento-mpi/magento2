<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api;

/**
 * Created from
 * @see \Magento\Catalog\Service\V1\Product\Link\ReadServiceInterface
 * @see \Magento\Catalog\Service\V1\Product\Link\WriteServiceInterface
 *
 * @todo Create new model that implements this interface \Magento\Catalog\Model\ProductLinkManagement
 */
interface ProductLinkManagementInterface
{
    /**
     * Provide the list of links for a specific product
     *
     * @param string $productSku
     * @param string $linkType
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return Data\ProductLinkInterface[]
     * @see \Magento\Catalog\Service\V1\Product\Link\ReadServiceInterface::getLinkedProducts - previous implementation
     */
    public function getList($productSku, $linkType);

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
