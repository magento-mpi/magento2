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
 * and delegate logic to \Magento\Catalog\Model\Resource\Product\Link::getAttributesByType
 */
interface TypeListInterface
{
    /**
     * Retrieve information about available product link types
     *
     * @return \Magento\Catalog\Api\Data\ProductLink\TypeInterface[]
     */
    public function getItems();

    /**
     * Provide a list of the product link type attributes
     *
     * @param string $type
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Magento\Catalog\Api\Data\ProductLink\AttributeInterface[]
     */
    public function getItemAttributes($type);
}
