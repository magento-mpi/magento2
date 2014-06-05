<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product;

interface GroupPriceServiceInterface
{
    /**
     * Set group price for product
     *
     * @param string $productSku
     * @param \Magento\Catalog\Service\V1\Data\Product\GroupPrice $price
     * @return \Magento\Catalog\Service\V1\Data\Product\GroupPrice
     */
    public function set($productSku, \Magento\Catalog\Service\V1\Data\Product\GroupPrice $price);

    /**
     * Remove group price from product
     *
     * @param string $productSku
     * @param string $customerGroupId
     * @return boolean
     */
    public function delete($productSku, $customerGroupId);

    /**
     * Retrieve list of product prices
     *
     * @param string $productSku
     * @return \Magento\Catalog\Service\V1\Data\Product\GroupPrice[]
     */
    public function getList($productSku);
}
