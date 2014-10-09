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
 */
interface GroupPriceServiceInterface
{
    /**
     * Set group price for product
     *
     * @param string $productSku
     * @param \Magento\Catalog\Service\V1\Data\Product\GroupPrice $price
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @deprecated
     * @see \Magento\Catalog\Api\GroupPriceManagementInterface::save
     */
    public function set($productSku, \Magento\Catalog\Service\V1\Data\Product\GroupPrice $price);

    /**
     * Remove group price from product
     *
     * @param string $productSku
     * @param int $customerGroupId
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @deprecated
     * @see \Magento\Catalog\Api\GroupPriceManagementInterface::delete
     */
    public function delete($productSku, $customerGroupId);

    /**
     * Retrieve list of product prices
     *
     * @param string $productSku
     * @return \Magento\Catalog\Service\V1\Data\Product\GroupPrice[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @deprecated
     * @see \Magento\Catalog\Api\GroupPriceManagementInterface::getList
     */
    public function getList($productSku);
}
