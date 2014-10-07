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

interface ProductTypeProviderInterface
{
    /**
     * Retrieve available product types
     *
     * @return \Magento\Catalog\Api\ProductTypeInterface[]
     */
    public function getProductTypes();
}
