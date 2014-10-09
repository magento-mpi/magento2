<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category\ProductLinks;

/**
 * @deprecated
 * @todo remove this interface
 */
interface ReadServiceInterface
{
    /**
     * @param int $categoryId
     * @return \Magento\Catalog\Service\V1\Data\Category\ProductLink[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     *
     * @deprecated
     * @see \Magento\Catalog\Api\CategoryLinkManagement::getList
     */
    public function assignedProducts($categoryId);
}
