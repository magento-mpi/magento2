<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link;

/**
 * @deprecated
 */
interface ReadServiceInterface
{
    /**
     * Provide the list of product link types
     *
     * @return \Magento\Catalog\Service\V1\Product\Link\Data\LinkType[]
     * @deprecated
     * @see \Magento\Catalog\Api\Data\ProductLink\TypeListInterface::getItems
     */
    public function getProductLinkTypes();

    /**
     * Provide the list of linked products for a specific product
     *
     * @param string $productSku
     * @param string $type
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Magento\Catalog\Service\V1\Product\Link\Data\ProductLink[]
     *
     * @deprecated
     * @see \Magento\Catalog\Api\ProductLinkManagementInterface::getList
     */
    public function getLinkedProducts($productSku, $type);

    /**
     * Provide a list of the product link type attributes
     *
     * @param string $type
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Magento\Catalog\Service\V1\Product\Link\Data\LinkAttribute[]
     *
     * @deprecated
     * @see \Magento\Catalog\Api\Data\ProductLink\TypeListInterface::getItemAttributes
     */
    public function getLinkAttributes($type);
}
