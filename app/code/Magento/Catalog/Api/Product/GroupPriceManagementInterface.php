<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Product;

/**
 * Interface GroupPriceManagementInterface must be implemented by new GroupPrice management model
 * @see \Magento\Catalog\Service\V1\Product\GroupPriceServiceInterface
 */
interface GroupPriceManagementInterface
{
    /**
     * Set group price for product
     *
     * @param string $productSku
     * @param \Magento\Catalog\Api\Data\Product\GroupPriceInterface $price
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @todo  add($productSku, $customerGroupId, $price, $websiteId=null);
     */
    public function add($productSku, \Magento\Catalog\Api\Data\Product\GroupPriceInterface $price);

    /**
     * Remove group price from product
     *
     * @param string $productSku
     * @param int $customerGroupId
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @todo remove($productSku, $customerGroupId, $websiteId = null)
     */
    public function remove($productSku, $customerGroupId);

    /**
     * Retrieve list of product prices
     *
     * @param string $productSku
     * @return \Magento\Catalog\Api\Data\Product\GroupPriceInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @todo getList($productSku, $websiteId = null);
     */
    public function getList($productSku);
}
