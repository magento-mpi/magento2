<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1;

interface CatalogProductLinkServiceInterface
{
    /**
     * Provide the list of product link types
     *
     * @return \Magento\Catalog\Service\V1\Data\CatalogProductLink[]
     */
    public function getProductLinkTypes();

    /**
     * Provide the list of linked products for a specific product
     *
     * @param int $productId
     * @param int $type
     * @return \Magento\Catalog\Service\V1\Data\CatalogProductLinkEntity[]
     */
    public function getLinkedProducts($productId, $type);
} 
