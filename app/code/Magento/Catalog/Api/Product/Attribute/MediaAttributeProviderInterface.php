<?php
/**
 * Product Media Attribute Provider
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Product\Attribute;

/**
 * @todo maybe make this method part of the attribute set interface.
 * Make sure to check if attribute set is a product attribute set.
 */
interface MediaAttributeProviderInterface
{
    /**
     * Retrieve the list of media attributes (fronted input type is media_image) assigned to the given attribute set.
     *
     * @param int $attributeSetId
     * @return mixed list of media attributes @todo use existing attribute interface
     */
    public function getMediaAttributes($attributeSetId);
}
