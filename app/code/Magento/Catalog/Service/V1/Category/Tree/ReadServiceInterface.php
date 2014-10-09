<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category\Tree;

use Magento\Catalog\Service\V1\Data\Eav\Category\Tree;

/**
 * Class ReadServiceInterface
 * @package Magento\Catalog\Service\V1\Category
 *
 * @todo remove this interface
 */
interface ReadServiceInterface
{
    /**
     * Retrieve list of product attribute types
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID is not found
     * @param int $rootCategoryId
     * @param int $depth
     * @return \Magento\Catalog\Service\V1\Data\Eav\Category\Tree containing Tree objects
     * @deprecated
     * @see \Magento\Catalog\Api\CategoryManagementInterface::getList
     */
    public function tree($rootCategoryId = null, $depth = null);
}
