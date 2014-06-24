<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category;

use Magento\Catalog\Service\V1\Data\Eav\Category\AttributeMetadata;

interface MetadataServiceInterface
{
    /**#@+
     * Predefined constants
     */
    const ATTRIBUTE_SET_ID_CATEGORY = 3;
    /**#@-*/

    /**
     * Retrieve custom EAV attribute metadata of category
     *
     * @return array<Data\Eav\Category\AttributeMetadata>
     */
    public function getCustomAttributesMetadata();

    /**
     * Retrieve EAV attribute metadata of category
     *
     * @return AttributeMetadata[]
     */
    public function getCategoryAttributesMetadata();

    /**
     * Returns all known attributes metadata for a given entity type
     *
     * @param  string $entityType
     * @param  int $attributeSetId
     * @param  int $storeId
     * @return AttributeMetadata[]
     */
    public function getAllAttributeSetMetadata($entityType, $attributeSetId = 0, $storeId = null);

    /**
     * Retrieve Attribute Metadata
     *
     * @param  string $entityType
     * @param  string $attributeCode
     * @return AttributeMetadata
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAttributeMetadata($entityType, $attributeCode);
}
