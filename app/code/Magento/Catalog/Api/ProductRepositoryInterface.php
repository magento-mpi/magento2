<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api;

use Magento\Framework\Data\Search\SearchCriteriaInterface;

interface ProductRepositoryInterface
{
    /**
     * Create product
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param array $arguments
     * @return int
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @see \Magento\Catalog\Service\V1\ProductServiceInterface::update - previous imlementation
     * @see \Magento\Catalog\Service\V1\ProductServiceInterface::create - previous imlementation
     */
    public function save(\Magento\Catalog\Api\Data\ProductInterface $product, array $arguments = []);

    /**
     * Get info about product by product SKU
     *
     * @param string $productSku
     * @param array $arguments
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($productSku, array $arguments = []);

    /**
     * Delete product
     *
     * @param int $productId product which will deleted
     * @param array $arguments
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see  \Magento\Catalog\Service\V1\ProductServiceInterface::delete
     */
    public function delete($productId, array $arguments = []);

    /**
     * Get product list
     *
     * @param \Magento\Framework\Data\Search\SearchCriteriaInterface $searchCriteria
     * @param array $arguments
     * @return \Magento\Catalog\Service\V1\Data\Product\SearchResults
     * @see \Magento\Catalog\Service\V1\ProductServiceInterface::search
     */
    public function getList(SearchCriteriaInterface $searchCriteria, array $arguments = []);
}
