<?php
/**
 * EAV attribute metadata service interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\Eav;

use Magento\Customer\Service\Entity\V1\Eav\AttributeMetadata;

interface AttributeMetadataServiceV1Interface
{
    /**
     * Retrieve Attribute Metadata
     *
     * @param   mixed $entityType
     * @param   mixed $attributeCode
     * @return AttributeMetadata
     */
    public function getAttributeMetadata($entityType, $attributeCode);

    /**
     * Retrieve Customer
     *
     * @param AttributeMetadata $attribute
     * @return AttributeMetadata
     */
    public function saveAttributeMetadata(AttributeMetadata $attribute);

    /**
     * Returns all known attributes metadata for a given entity type
     *
     * @param string $entityType
     * @param int $attributeSetId
     * @param int $storeId
     * @return AttributeMetadata[]
     */
    public function getAllAttributeSetMetadata($entityType, $attributeSetId = 0, $storeId = null);

    /**
     * Retrieve all attributes for entityType filtered by form code
     *
     * @param $entityType
     * @param $formCode
     * @return AttributeMetadata[]
     */
    public function getAttributes($entityType, $formCode);
}
