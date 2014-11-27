<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Api;

interface ProductLinkManagementInterface
{
    /**
     * Get all children for Bundle product
     *
     * @param string $productId
     * @return \Magento\Bundle\Api\Data\LinkInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Webapi\Exception
     * @see \Magento\Bundle\Service\V1\Product\Link\ReadServiceInterface::getChildren
     */
    public function getChildren($productId);

    /**
     * Add child product to specified Bundle option by product sku
     *
     * @param string $productSku
     * @param int $optionId
     * @param \Magento\Bundle\Api\Data\LinkInterface $linkedProduct
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @return int
     * @see \Magento\Bundle\Service\V1\Product\Link\WriteServiceInterface::addChild
     */
    public function addChildByProductSku($productSku, $optionId, \Magento\Bundle\Api\Data\LinkInterface $linkedProduct);

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param int $optionId
     * @param Data\LinkInterface $linkedProduct
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @return int
     */
    public function addChild(
        \Magento\Catalog\Api\Data\ProductInterface $product,
        $optionId,
        \Magento\Bundle\Api\Data\LinkInterface $linkedProduct
    );

    /**
     * Remove product from Bundle product option
     *
     * @param string $productSku
     * @param int $optionId
     * @param string $childSku
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Webapi\Exception
     * @return bool
     * @see \Magento\Bundle\Service\V1\Product\Link\WriteServiceInterface::removeChild
     */
    public function removeChild($productSku, $optionId, $childSku);
}
