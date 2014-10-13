<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Product\Link;
/**
 * Implementation: @see \Magento\Catalog\Model\Product\LinkTypeProvider
 * Add method \Magento\Catalog\Model\Product\LinkTypeProvider::getLinkAttributes($type);
 * and delegate logic to @see \Magento\Catalog\Model\Resource\Product\Link::getAttributesByType
 *
 * @see \Magento\Catalog\Service\V1\Product\Link\ReadServiceInterface - previous implementation
 */
interface ProductLinkTypeListInterface
{
    /**
     * Retrieve information about available product link types
     *
     * @return \Magento\Catalog\Api\Data\ProductLinkTypeInterface[]
     * @see \Magento\Catalog\Service\V1\Product\Link\ReadServiceInterface::getProductLinkTypes - previous implementation
     */
    public function getItems();

    /**
     * Provide a list of the product link type attributes
     *
     * @param string $type
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Magento\Catalog\Api\Data\ProductLinkAttributeInterface[]
     * @see \Magento\Catalog\Service\V1\Product\Link\ReadServiceInterface::getLinkAttributes - previous implementation
     */
    public function getItemAttributes($type);
}
