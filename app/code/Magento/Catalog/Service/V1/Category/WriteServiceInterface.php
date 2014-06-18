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
     * Delete category

     *
     * @param int $categoryId category which will deleted
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function delete($categoryId);
}
