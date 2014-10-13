<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Category;

/**
 * Interface RepositoryInterface must be implemented in new model \Magento\Catalog\Model\CategoryRepository
 * @todo use standard repo interface
 */
interface CategoryRepositoryInterface
{
    /**
     * Create category service
     *
     * @param \Magento\Catalog\Api\Data\CategoryInterface $category
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magento\Catalog\Api\Data\CategoryInterface $category);

    /**
     * Get info about category by category id
     *
     * @param int $categoryId
     * @return \Magento\Catalog\Api\Data\CategoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
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
     */
    public function delete($categoryId);
}
