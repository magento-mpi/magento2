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
 * Create new model that implements this interface \Magento\Catalog\Model\ProductManagement
 */
interface ProductManagementInterface
{
    /**
     * Get product info
     *
     * @param  string $id
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID is not found
     * @return \Magento\Catalog\Api\Data\ProductInterface $product
     */
    public function get($id);

    /**
     * Delete product
     *
     * @param  string $id
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID is not found
     * @throws \Exception If something goes wrong during delete
     * @return bool True if the entity was deleted (always true)
     */
    public function delete($id);

    /**
     * Save product process
     *
     * @param  \Magento\Catalog\Api\Data\ProductInterface $product
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a ID is sent but the entity does not exist
     * @throws \Magento\Framework\Model\Exception If something goes wrong during save
     * @return string id
     */
    public function create(\Magento\Catalog\Api\Data\ProductInterface $product);

    /**
     * Update product process
     *
     * @param  string $id
     * @param  \Magento\Catalog\Api\Data\ProductInterface $product
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a ID is sent but the entity does not exist
     * @throws \Magento\Framework\Model\Exception If something goes wrong during save
     * @return string id
     */
    public function update($id, \Magento\Catalog\Api\Data\ProductInterface $product);

    /**
     * Get product list
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Catalog\Service\V1\Data\Product\SearchResults containing Data\ProductInterface objects
     */
    public function search(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);
}
