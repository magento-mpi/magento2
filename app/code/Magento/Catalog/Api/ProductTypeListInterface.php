<?php
/**
 * Product type provider
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api;

/**
 * Create from:
 * @see \Magento\Catalog\Service\V1\ProductTypeServiceInterface
 */
interface ProductTypeListInterface
{
    /**
     * Retrieve available product types
     *
     * @return \Magento\Catalog\Api\Data\ProductTypeInterface[]
     *
     * @see \Magento\Catalog\Service\V1\ProductTypeServiceInterface::getProductTypes
     */
    public function getProductTypes();
}
