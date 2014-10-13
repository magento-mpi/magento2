<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api;

interface ProductMediaAttributeManagementInterface
{
    /**
     * Retrieve the list of media attributes (fronted input type is media_image) assigned to the given attribute set.
     *
     * @param string $attributeSetName
     * @param string $entityType
     * @return array list of media attributes
     * @see \Magento\Catalog\Service\V1\Product\Attribute\Media\ReadServiceInterface::types
     */
    public function getList($attributeSetName, $entityType);
}
