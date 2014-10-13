<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api;

/**
 * @todo create new repository class
 */
interface ProductRepositoryInterface
{
    /**
     * Create product
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $category
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @see \Magento\Catalog\Service\V1\ProductServiceInterface::update - previous imlementation
     * @see \Magento\Catalog\Service\V1\ProductServiceInterface::create - previous imlementation
     */
    public function save(\Magento\Catalog\Api\Data\ProductInterface $product);

    /**
     * Get info about product by product id
     *
     * @param int $categoryId
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Magento\Catalog\Service\V1\ProductServiceInterface::get
     */
    public function get($productId);

    /**
     * Delete product
     *
     * @param int $productId product which will deleted
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see  \Magento\Catalog\Service\V1\ProductServiceInterface::delete
     */
    public function delete($productId);

    /**
     * Get product list
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     * @see \Magento\Catalog\Service\V1\ProductServiceInterface::search
     */
    public function getList(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);
}
