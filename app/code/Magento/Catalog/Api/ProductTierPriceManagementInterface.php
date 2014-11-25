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
     */
    public function remove($productSku, $customerGroupId, $qty);

    /**
     * Get tire price of product
     *
     * @param string $productSku
     * @param string $customerGroupId
     * @return \Magento\Catalog\Api\Data\ProductTierPriceInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList($productSku, $customerGroupId);
}
