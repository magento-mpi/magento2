<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category;
/**
 * @todo remove this interface
 */
interface ReadServiceInterface
{
    /**
     * Get info about category by category id
     *
     * @param int $categoryId
     * @return \Magento\Catalog\Service\V1\Data\Eav\Category\Info\Metadata
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @deprecated
     * @see \Magento\Catalog\Api\CategoryRepositoryInterface::get
     */
    public function info($categoryId);
}
