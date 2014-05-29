<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Catalog\Service\V1\Product\Link;

interface WriteServiceInterface
{
    /**
     * Assign a product link to another product
     *
     * @param string $productSku
     * @param \Magento\Catalog\Service\V1\Product\Link\Data\ProductLinkEntity[] $assignedProducts
     * @param string $type
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     */
    public function assign($productSku, array $assignedProducts, $type);

    /**
     * Update product link
     *
     * @param string $productSku
     * @param \Magento\Catalog\Service\V1\Product\Link\Data\ProductLinkEntity $linkedProduct
     * @param string $type
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     */
    public function update($productSku, Data\ProductLinkEntity $linkedProduct, $type);

    /**
     * Remove the product link from a specific product
     *
     * @param string $productSku
     * @param string $linkedProductSku
     * @param string $type
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     */
    public function remove($productSku, $linkedProductSku, $type);
}
