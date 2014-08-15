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
 * Interface CustomerMetadataServiceInterface
 */
interface CustomerMetadataServiceInterface extends MetadataServiceInterface
{
    const ATTRIBUTE_SET_ID_CUSTOMER = 1;

    const ENTITY_TYPE_CUSTOMER = 'customer';

    const DATA_OBJECT_CLASS_NAME = 'Magento\Customer\Service\V1\Data\Customer';

    /**
     * Retrieve all attributes filtered by form code
     *
     * @param string $formCode
     * @return \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata[]
     */
    public function getAttributes($formCode);

    /**
     * Retrieve Customer EAV attribute metadata
     *
     * @param string $attributeCode
     * @return \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAttributeMetadata($attributeCode);

    /**
     * Get all attribute metadata for customers
     *
     * @return \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata[]
     */
    public function getAllAttributesMetadata();

    /**
     *{@inheritdoc}
     */
    public function getCustomAttributesMetadata($dataObjectClassName = self::DATA_OBJECT_CLASS_NAME);
}
