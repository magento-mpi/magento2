<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category;

interface ReadServiceInterface
{
    /**
     * Get info about category by category id
     *
     * @param int $categoryId
     * @return \Magento\Catalog\Service\V1\Data\Eav\Category\AttributeMetadata
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function info($categoryId);
}
