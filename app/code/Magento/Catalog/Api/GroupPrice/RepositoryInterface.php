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
 * Interface Group/RepositoryInterface must be implemented by new Group Price repository model
 */
interface RepositoryInterface
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
    public function add($productSku, \Magento\Catalog\Api\Data\GroupPriceInterface $price);

    /**
     * Remove group price from product
     *
     * @param string $productSku
     * @param int $customerGroupId
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function remove($productSku, $customerGroupId);

    /**
     * Retrieve list of product prices
     *
     * @param string $productSku
     * @return \Magento\Catalog\Service\V1\Data\Product\GroupPrice[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($productSku);
}

