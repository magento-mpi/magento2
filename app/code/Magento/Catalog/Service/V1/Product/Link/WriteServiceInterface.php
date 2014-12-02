<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Catalog\Service\V1\Product\Link;

/**
 * @todo remove this interface
 * @deprecated
 */
interface WriteServiceInterface
{
    /**
     * Assign a product link to another product
     *
     * @param string $productSku
     * @param \Magento\Catalog\Service\V1\Product\Link\Data\ProductLink[] $assignedProducts
     * @param string $type
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     *
     * @deprecated
     * @see \Magento\Catalog\Api\ProductLinkManagementInterface::assign
     */
    public function assign($productSku, array $assignedProducts, $type);

    /**
     * Update product link
     *
     * @param string $productSku
     * @param \Magento\Catalog\Service\V1\Product\Link\Data\ProductLink $linkedProduct
     * @param string $type
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     *
     * @deprecated
     * @see \Magento\Catalog\Api\ProductLinkManagementInterface::update
     */
    public function update($productSku, Data\ProductLink $linkedProduct, $type);

    /**
     * Remove the product link from a specific product
     *
     * @param string $productSku
     * @param string $linkedProductSku
     * @param string $type
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     *
     * @deprecated
     * @see \Magento\Catalog\Api\ProductLinkManagementInterface::remove
     */
    public function remove($productSku, $linkedProductSku, $type);
}
