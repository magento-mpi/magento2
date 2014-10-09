<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\GroupPrice;

/**
 * Interface GroupPriceManagementInterface must be implemented by new GroupPrice management model
 */
interface GroupPriceManagementInterface
{

    /**
     * Set group price for product
     *
     * @param string $productSku
     * @param \Magento\Catalog\Api\Data\GroupPriceInterface $price
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save($productSku, \Magento\Catalog\Api\Data\GroupPriceInterface $price);

    /**
     * Remove group price from product
     *
     * @param string $productSku
     * @param int $customerGroupId
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function delete($productSku, $customerGroupId);

    /**
     * Retrieve list of product prices
     *
     * @param string $productSku
     * @return \Magento\Catalog\Api\Data\GroupPriceInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList($productSku);
}
