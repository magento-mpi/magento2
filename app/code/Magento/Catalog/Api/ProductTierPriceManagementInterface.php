<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api;

interface ProductTierPriceManagementInterface
{
    /**
     * Create tire price for product
     *
     * @param string $productSku
     * @param string $customerGroupId
     * @param float $price
     * @param float $qty
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @see \Magento\Catalog\Service\V1\Product\TierPriceServiceInterface::set
     */
    public function add($productSku, $customerGroupId, $price, $qty);

    /**
     * Remove tire price from product
     *
     * @param string $productSku
     * @param string $customerGroupId
     * @param float $qty
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @see \Magento\Catalog\Service\V1\Product\TierPriceServiceInterface::delete
     */
    public function remove($productSku, $customerGroupId, $qty);

    /**
     * Get tire price of product
     *
     * @param string $productSku
     * @param string $customerGroupId
     * @return \Magento\Catalog\Api\Data\ProductTierPriceInterface[]
     * @see \Magento\Catalog\Service\V1\Product\TierPriceServiceInterface::getList
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Magento\Catalog\Service\V1\Product\TierPriceServiceInterface::getList
     */
    public function getList($productSku, $customerGroupId);
}
