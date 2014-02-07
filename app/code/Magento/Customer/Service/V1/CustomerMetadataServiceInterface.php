<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

/**
 * Manipulate Customer Metadata Attributes *
 */
interface CustomerMetadataServiceInterface
{
    const CUSTOMER_ATTRIBUTE_SET_ID = 1;
    const ADDRESS_ATTRIBUTE_SET_ID = 2;

    /**
     * Retrieve Attribute Metadata
     *
     * @param   mixed $entityType
     * @param   mixed $attributeCode
     * @return Dto\Eav\AttributeMetadata
     */
    public function getAttributeMetadata($entityType, $attributeCode);

    /**
     * Returns all known attributes metadata for a given entity type
     *
     * @param string $entityType
     * @param int $attributeSetId
     * @param int $storeId
     * @return Dto\Eav\AttributeMetadata[]
     */
    public function getAllAttributeSetMetadata($entityType, $attributeSetId = 0, $storeId = null);

    /**
     * Retrieve all attributes for entityType filtered by form code
     *
     * @param $entityType
     * @param $formCode
     * @return Dto\Eav\AttributeMetadata[]
     */
    public function getAttributes($entityType, $formCode);

    /**
     * Retrieve Customer EAV attribute metadata
     *
     * @param string $attributeCode
     * @return Dto\Eav\AttributeMetadata
     */
    public function getCustomerAttributeMetadata($attributeCode);

    /**
     * Returns all attribute metadata for customers
     *
     * @return Dto\Eav\AttributeMetadata[]
     */
    public function getAllCustomerAttributeMetadata();

    /**
     * Retrieve Customer Addresses EAV attribute metadata
     *
     * @param string $attributeCode
     * @return Dto\Eav\AttributeMetadata
     */
    public function getAddressAttributeMetadata($attributeCode);

    /**
     * Returns all attribute metadata for Addresses
     *
     * @return Dto\Eav\AttributeMetadata[]
     */
    public function getAllAddressAttributeMetadata();

}
