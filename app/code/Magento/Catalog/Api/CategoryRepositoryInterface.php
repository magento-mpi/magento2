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
 * Interface RepositoryInterface must be implemented in new model \Magento\Catalog\Model\CategoryRepository
 */
interface CategoryRepositoryInterface
{
    /**
     * Create category service
     *
     * @param \Magento\Catalog\Api\Data\CategoryInterface $category
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @see \Magento\Catalog\Service\V1\Category\WriteServiceInterface::create
     */
    public function save(\Magento\Catalog\Api\Data\CategoryInterface $category);

    /**
     * Get info about category by category id
     *
     * @param int $categoryId
     * @return \Magento\Catalog\Api\Data\CategoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Magento\Catalog\Service\V1\Category\ReadServiceInterface::info
     */
    public function get($categoryId);

    /**
     * Delete category
     *
     * @param int $categoryId category which will deleted
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Magento\Catalog\Service\V1\Category\WriteServiceInterface::delete
     */
    public function delete($categoryId);

    /**
     * Get category list
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Catalog\Api\Data\CategoryInterface[]
     */
    public function getList(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);
}
