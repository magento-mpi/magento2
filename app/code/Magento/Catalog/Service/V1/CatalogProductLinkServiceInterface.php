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
     * @return \Magento\Catalog\Service\V1\Data\LinkTypeEntity[]
     */
    public function getProductLinkTypes();

    /**
     * Provide the list of linked products for a specific product
     *
     * @param int $productId
     * @param int $type
     * @throws \Magento\Framework\Exception\InputException If link type is not registered
     * @return \Magento\Catalog\Service\V1\Data\LinkedProductEntity[]
     */
    public function getLinkedProducts($productId, $type);

    /**
     * Provide a list of the product link type attributes
     *
     * @param int $type
     * @return \Magento\Catalog\Service\V1\Data\LinkAttributeEntity[]
     */
    public function getLinkAttributes($type);
}
