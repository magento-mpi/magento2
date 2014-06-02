<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

/**
 * Class ProductServiceInterface
 * @package Magento\Catalog\Service\V1
 */
interface ProductServiceInterface
{
    /**
     * Get product info
     *
     * @param  string $sku
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID is not found
     * @return \Magento\Catalog\Service\V1\Data\Product $product
     */
    public function get($sku);

    /**
     * Delete product
     *
     * @param  string $sku
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID is not found
     * @throws \Exception If something goes wrong during delete
     * @return bool True if the entity was deleted (always true)
     */
    public function delete($sku);

    /**
     * Save product process
     *
     * @param  \Magento\Catalog\Service\V1\Data\Product $product
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a ID is sent but the entity does not exist
     * @throws \Magento\Framework\Model\Exception If something goes wrong during save
     * @return string sku
     */
    public function create(\Magento\Catalog\Service\V1\Data\Product $product);

    /**
     * Update product process
     *
     * @param  string $sku
     * @param  \Magento\Catalog\Service\V1\Data\Product $product
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a ID is sent but the entity does not exist
     * @throws \Magento\Framework\Model\Exception If something goes wrong during save
     * @return string sku
     */
    public function update($sku, \Magento\Catalog\Service\V1\Data\Product $product);

    /**
     * get product list product
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Catalog\Service\V1\Data\SearchResults containing Data\Product objects
     */
    public function search(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);
}
