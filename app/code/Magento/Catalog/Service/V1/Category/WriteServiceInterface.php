<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category;

interface WriteServiceInterface
{
    /**
     * Create category service
     *
     * @param \Magento\Catalog\Service\V1\Data\Category $category
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function create(\Magento\Catalog\Service\V1\Data\Category $category);

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

    /**
     * Update category
     *
     * @param int $categoryId category to be updated
     * @param \Magento\Catalog\Service\V1\Data\Category $category
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function update($categoryId, \Magento\Catalog\Service\V1\Data\Category $category);

    /**
     * Move category
     *
     * @param int $categoryId
     * @param int $parentId
     * @param int $afterId
     * @return bool
     * @throws \Magento\Framework\Model\Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function move($categoryId, $parentId, $afterId = null);
}
