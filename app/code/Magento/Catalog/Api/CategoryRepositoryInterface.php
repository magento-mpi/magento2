<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api;

interface CategoryRepositoryInterface
{
    /**
     * Create category service
     *
     * @param \Magento\Catalog\Api\Data\CategoryDetailsInterface $category
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magento\Catalog\Api\Data\CategoryDetailsInterface $category);

    /**
     * Get info about category by category id
     *
     * @param int $categoryId
     * @return \Magento\Catalog\Api\Data\CategoryDetailsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($categoryId);

    /**
     * Delete category by identifier
     *
     * @param \Magento\Catalog\Api\Data\CategoryDetailsInterface $category category which will deleted
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function delete(\Magento\Catalog\Api\Data\CategoryDetailsInterface $category);


    /**
     * Delete category by identifier
     *
     * @param int $categoryId
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteByIdentifier($categoryId);

}
