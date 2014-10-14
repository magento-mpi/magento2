<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api;

/**
 * Interface GroupPriceManagementInterface must be implemented by new GroupPrice management model
 * @see \Magento\Catalog\Service\V1\Product\GroupPriceServiceInterface
 */
interface ProductGroupPriceManagementInterface
{
    /**
     * Set group price for product
     *
     * @param string $productSku
     * @param \Magento\Catalog\Api\Data\ProductGroupPriceInterface $price
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @see \Magento\Catalog\Service\V1\Product\GroupPriceServiceInterface::set
     */
    public function add($productSku, $customerGroupId, $price, $websiteId = null);

    /**
     * Remove group price from product
     *
     * @param string $productSku
     * @param int $customerGroupId
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @see \Magento\Catalog\Service\V1\Product\GroupPriceServiceInterface::delete
     */
    public function remove($productSku, $customerGroupId, $websiteId = null);

    /**
     * Retrieve list of product prices
     *
     * @param string $productSku
     * @return \Magento\Catalog\Api\Data\ProductGroupPriceInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Magento\Catalog\Service\V1\Product\GroupPriceServiceInterface::getList
     */
    public function getList($productSku, $websiteId = null);
}
