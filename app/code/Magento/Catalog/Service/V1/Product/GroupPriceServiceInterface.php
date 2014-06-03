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
     * @param string $productSku
     * @param string $customerGroupId
     * @param double $price
     * @param double $qty
     */
    public function create($productSku, $customerGroupId, $price, $qty = null);

    /**
     * @param string $productSku
     * @param string $customerGroupId
     * @param double $qty
     */
    public function delete($productSku, $customerGroupId, $qty = null);

    /**
     * @param string $productSku
     */
    public function getList($productSku);

    /**
     * @param string $productSku
     * @param string $customerGroupId
     * @param double $qty
     */
    public function get($productSku, $customerGroupId, $qty = null);
}
