<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category\ProductLinks;

use Magento\Catalog\Service\V1\Data\Eav\Category\ProductLink;

interface WriteServiceInterface
{
    /**
     * Delete category
     *
     * @param int $categoryId
     * @param \Magento\Catalog\Service\V1\Data\Eav\Category\ProductLink $productLink
     * @return bool Will returned True if assigned
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function assignProduct($categoryId, ProductLink $productLink);
}
