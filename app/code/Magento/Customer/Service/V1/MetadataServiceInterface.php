<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

use Magento\Framework\Api\MetadataServiceInterface as EavMetadataServiceInterface;

/**
 * Interface providing APIs to fetch custom attributes metadata
 */
interface MetadataServiceInterface extends EavMetadataServiceInterface
{
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

    /**
     *  Get custom attribute metadata for the given Data object's attribute set
     *
     * @param string $dataObjectClassName Data object class name
     * @return \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata[]
     */
    public function getCustomAttributesMetadata($dataObjectClassName = '');
}
