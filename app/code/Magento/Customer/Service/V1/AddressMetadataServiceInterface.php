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
 * Interface providing apis to fetch Address related custom attributes
 */
interface AddressMetadataServiceInterface extends MetadataServiceInterface
{
    const ATTRIBUTE_SET_ID_ADDRESS = 2;

    const ENTITY_TYPE_ADDRESS = 'customer_address';

    /**
     * Retrieve Customer Addresses EAV attribute metadata
     *
     * @param string $attributeCode
     * @return \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAddressAttributeMetadata($attributeCode);

    /**
     * Get all attribute metadata for Addresses
     *
     * @return \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata[]
     */
    public function getAllAddressAttributeMetadata();

    /**
     * Get custom attribute metadata for customer address.
     *
     * @return \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata[]
     */
    public function getCustomAddressAttributeMetadata();

}
