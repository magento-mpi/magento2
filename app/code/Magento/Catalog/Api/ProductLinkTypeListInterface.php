<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api;
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
     */
    public function getItems();

    /**
     * Provide a list of the product link type attributes
     *
     * @param string $type
     * @return \Magento\Catalog\Api\Data\ProductLinkAttributeInterface[]
     */
    public function getItemAttributes($type);
}
