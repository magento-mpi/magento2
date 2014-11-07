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

interface ProductTypeListInterface
{
    /**
     * Retrieve available product types
     *
     * @return \Magento\Catalog\Api\Data\ProductTypeInterface[]
     */
    public function getProductTypes();
}
