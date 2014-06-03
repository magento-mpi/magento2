<?php
/**
 * Product type service interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

interface ProductTypeServiceInterface
{
    /**
     * Retrieve the list of product types
     *
     * @return \Magento\Catalog\Service\V1\Data\ProductType[]
     */
    public function getProductTypes();
}
