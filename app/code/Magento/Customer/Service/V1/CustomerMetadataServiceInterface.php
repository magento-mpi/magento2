<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

use Magento\Exception\NoSuchEntityException;

/**
 * Manipulate Customer Metadata Attributes *
 */
interface CustomerMetadataServiceInterface
{
    const ATTRIBUTE_SET_ID_CUSTOMER = 1;
    const ATTRIBUTE_SET_ID_ADDRESS = 2;
    const ENTITY_TYPE_CUSTOMER = 'customer';
    const ENTITY_TYPE_ADDRESS = 'customer_address';

    /**
     * Retrieve Attribute Metadata
     *
     * @param   mixed $entityType
     * @param   mixed $attributeCode
     * @return Data\Eav\AttributeMetadata
     * @throws NoSuchEntityException
     */
    public function getAttributeMetadata($entityType, $attributeCode);

    /**
     * Returns all known attributes metadata for a given entity type
     *
     * @param string $entityType
     * @param int $attributeSetId
     * @param int $storeId
     * @return Data\Eav\AttributeMetadata[]
     */
    public function getAllAttributeSetMetadata($entityType, $attributeSetId = 0, $storeId = null);

    /**
     * Retrieve all attributes for entityType filtered by form code
     *
     * @param $entityType
     * @param $formCode
     * @return Data\Eav\AttributeMetadata[]
     */
    public function getAttributes($entityType, $formCode);

    /**
     * Retrieve Customer EAV attribute metadata
     *
     * @param string $attributeCode
     * @return Data\Eav\AttributeMetadata
     * @throws NoSuchEntityException
     */
    public function getCustomerAttributeMetadata($attributeCode);

    /**
     * Get all attribute metadata for customers
     *
     * @return Data\Eav\AttributeMetadata[]
     */
    public function getAllCustomerAttributeMetadata();

    /**
     * Get custom attribute metadata for customer.
     *
     * @return Data\Eav\AttributeMetadata[]
     */
    public function getCustomCustomerAttributeMetadata();

    /**
     * Retrieve Customer Addresses EAV attribute metadata
     *
     * @param string $attributeCode
     * @return Data\Eav\AttributeMetadata
     * @throws NoSuchEntityException
     */
    public function getAddressAttributeMetadata($attributeCode);

    /**
     * Get all attribute metadata for Addresses
     *
     * @return Data\Eav\AttributeMetadata[]
     */
    public function getAllAddressAttributeMetadata();

    /**
     * Get custom attribute metadata for customer address.
     *
     * @return Data\Eav\AttributeMetadata[]
     */
    public function getCustomAddressAttributeMetadata();
}
