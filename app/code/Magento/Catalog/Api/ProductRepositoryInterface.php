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
use Magento\Catalog\Api\Data\ProductInterface;

interface ProductRepositoryInterface
{
    /**
     * Create product
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return int
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @see \Magento\Catalog\Service\V1\ProductServiceInterface::update - previous imlementation
     * @see \Magento\Catalog\Service\V1\ProductServiceInterface::create - previous imlementation
     */
    public function save(ProductInterface $product);

    /**
     * Get info about product by product SKU
     *
     * @param string $productSku
     * @param bool $editMode
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($productSku, $editMode = false);

    /**
     * Delete product
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see  \Magento\Catalog\Service\V1\ProductServiceInterface::delete
     */
    public function delete(ProductInterface $product);

    /**
     * Get product list
     *
     * @param \Magento\Framework\Data\Search\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Catalog\Service\V1\Data\Product\SearchResults
     * @see \Magento\Catalog\Service\V1\ProductServiceInterface::search
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
