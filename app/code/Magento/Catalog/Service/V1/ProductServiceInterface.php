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
 * @deprecated
 * @todo remove this interface
 */
interface ProductServiceInterface
{
    /**
     * Get product info
     *
     * @param  string $id
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID is not found
     * @return \Magento\Catalog\Service\V1\Data\Product $product
     * @deprecated
     * @see \Magento\Catalog\Api\ProductRepositoryInterface::get
     */
    public function get($id);

    /**
     * Delete product
     *
     * @param  string $id
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID is not found
     * @throws \Exception If something goes wrong during delete
     * @return bool True if the entity was deleted (always true)
     * @deprecated
     * @see \Magento\Catalog\Api\ProductRepositoryInterface::delete
     */
    public function delete($id);

    /**
     * Save product process
     *
     * @param  \Magento\Catalog\Service\V1\Data\Product $product
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a ID is sent but the entity does not exist
     * @throws \Magento\Framework\Model\Exception If something goes wrong during save
     * @return string id
     * @deprecated
     * @see \Magento\Catalog\Api\ProductRepositoryInterface::save
     */
    public function create(\Magento\Catalog\Service\V1\Data\Product $product);

    /**
     * Update product process
     *
     * @param  string $id
     * @param  \Magento\Catalog\Service\V1\Data\Product $product
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a ID is sent but the entity does not exist
     * @throws \Magento\Framework\Model\Exception If something goes wrong during save
     * @return string id
     * @deprecated
     * @see \Magento\Catalog\Api\ProductManagementInterface::update
     */
    public function update($id, \Magento\Catalog\Service\V1\Data\Product $product);

    /**
     * Get product list
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Catalog\Service\V1\Data\Product\SearchResults containing Data\Product objects
     * @deprecated
     * @see \Magento\Catalog\Api\ProductRepositoryInterface::getList
     */
    public function search(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);
}
