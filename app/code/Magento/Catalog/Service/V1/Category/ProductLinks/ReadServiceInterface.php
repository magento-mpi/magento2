<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category\ProductLinks;

interface ReadServiceInterface
{
    /**
     * @param int $categoryId
     * @return \Magento\Catalog\Service\V1\Data\Category\ProductLink[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function assignedProducts($categoryId);
}
