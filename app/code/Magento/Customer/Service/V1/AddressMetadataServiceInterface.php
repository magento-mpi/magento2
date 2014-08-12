<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

use Magento\Framework\Service\Data\Eav\MetadataServiceInterface;

/**
 * Interface providing APIs to fetch Address related custom attributes
 */
interface AddressMetadataServiceInterface extends MetadataServiceInterface
{
    const ATTRIBUTE_SET_ID_ADDRESS = 2;

    const ENTITY_TYPE_ADDRESS = 'customer_address';

    /**
     * Retrieve all attributes for entityType filtered by form code
     *
     * @param string $formCode
     * @return \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata[]
     */
    public function getAttributes($formCode);

    /**
     * Retrieve Customer Addresses EAV attribute metadata
     *
     * @param string $attributeCode
     * @return \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAttributeMetadata($attributeCode);

    /**
     * Get all attribute metadata for Addresses
     *
     * @return \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata[]
     */
    public function getAllAttributesMetadata();
}
