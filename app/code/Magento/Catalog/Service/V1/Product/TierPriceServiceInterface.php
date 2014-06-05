<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product;

interface TierPriceServiceInterface
{
    /**
     * @param string $productSku
     * @param \Magento\Catalog\Service\V1\Data\Product\TierPrice $price
     * @return \Magento\Catalog\Service\V1\Data\Product\TierPrice
     */
    public function create($productSku, \Magento\Catalog\Service\V1\Data\Product\TierPrice $price);

    /**
     * @param string $productSku
     * @param string $customerGroupId
     * @param double $qty
     * @return boolean
     */
    public function delete($productSku, $customerGroupId, $qty);

    /**
     * @param string $productSku
     * @param string $customerGroupId
     * @param double $qty
     * @return \Magento\Catalog\Service\V1\Data\Product\TierPrice
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($productSku, $customerGroupId, $qty);

    /**
     * @param string $productSku
     * @param string $customerGroupId
     * @return \Magento\Catalog\Service\V1\Data\Product\TierPrice[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList($productSku, $customerGroupId);
} 
