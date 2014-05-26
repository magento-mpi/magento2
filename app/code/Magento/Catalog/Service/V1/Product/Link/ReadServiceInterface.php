<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link;

interface ReadServiceInterface
{
    /**
     * Provide the list of product link types
     *
     * @return \Magento\Catalog\Service\V1\Product\Link\Data\LinkTypeEntity[]
     */
    public function getProductLinkTypes();

    /**
     * Provide the list of linked products for a specific product
     *
     * @param string $productSku
     * @param string $type
     * @throws \Magento\Framework\Exception\InputException If link type is not registered
     * @throws \Magento\Framework\Exception\InputException If the product with provided SKU is missed
     * @return \Magento\Catalog\Service\V1\Product\Link\Data\LinkedProductEntity[]
     */
    public function getLinkedProducts($productSku, $type);

    /**
     * Provide a list of the product link type attributes
     *
     * @param string $type
     * @throws \Magento\Framework\Exception\InputException If link type is not registered
     * @return \Magento\Catalog\Service\V1\Product\Link\Data\LinkAttributeEntity[]
     */
    public function getLinkAttributes($type);
}
