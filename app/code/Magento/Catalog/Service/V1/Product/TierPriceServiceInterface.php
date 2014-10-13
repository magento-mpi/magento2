<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product;

/**
 * @todo remove this interface
 * @see \Magento\Catalog\Api\Product\TierPriceManagementInterface
 */
interface TierPriceServiceInterface
{
    /**
     * Create tire price for product
     *
     * @param string $productSku
     * @param string $customerGroupId
     * @param \Magento\Catalog\Service\V1\Data\Product\TierPrice $price
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @deprecated
     * @see \Magento\Catalog\Api\ProductTierPriceManagementInterface::add
     */
    public function set($productSku, $customerGroupId, \Magento\Catalog\Service\V1\Data\Product\TierPrice $price);

    /**
     * Remove tire price from product
     *
     * @param string $productSku
     * @param string $customerGroupId
     * @param float $qty
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @deprecated
     * @see \Magento\Catalog\Api\ProductTierPriceManagementInterface::remove
     */
    public function delete($productSku, $customerGroupId, $qty);

    /**
     * Get tire price of product
     *
     * @param string $productSku
     * @param string $customerGroupId
     * @return \Magento\Catalog\Service\V1\Data\Product\TierPrice[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @deprecated
     * @see \Magento\Catalog\Api\ProductTierPriceManagementInterface::getList
     */
    public function getList($productSku, $customerGroupId);
}
