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
     */
    public function tree($rootCategoryId = null, $depth = null);
}
