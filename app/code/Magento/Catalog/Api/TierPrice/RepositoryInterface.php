<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\TierPrice;

/**
 * Interface Tier/RepositoryInterface must be implemented by new Group Price repository model
 */
interface RepositoryInterface
{
    /**
     * Create tire price for product
     *
     * @param string $productSku
     * @param string $customerGroupId
     * @param \Magento\Catalog\Api\Data\TierPriceInterface $price
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save($productSku, $customerGroupId, \Magento\Catalog\Api\Data\TierPriceInterface $price);

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
    public function delete($productSku, $customerGroupId, $qty);

    /**
     * Get tire price of product
     *
     * @param string $productSku
     * @param string $customerGroupId
     * @return \Magento\Catalog\Api\Data\TierPriceInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList($productSku, $customerGroupId);
}
