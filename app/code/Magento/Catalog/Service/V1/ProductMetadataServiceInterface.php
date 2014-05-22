<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

/**
 * Class ProductMetadataServiceInterface
 * @package Magento\Catalog\Service\V1
 */
interface ProductMetadataServiceInterface
{
    /**#@+
     * Predefined constants
     */
    const ENTITY_TYPE_PRODUCT           = 'catalog_product';

    const ATTRIBUTE_SET_ID_PRODUCT      = 4;
    /**#@-*/

    /**
     * Retrieve custom EAV attribute metadata of product
     *
     * @return array<Data\Eav\AttributeMetadata>
     */
    public function getCustomAttributesMetadata();

    /**
     * Retrieve EAV attribute metadata of product
     *
     * @return Data\Eav\AttributeMetadata[]
     */
    public function getProductAttributesMetadata();

    /**
     * Returns all known attributes metadata for a given entity type
     *
     * @param  string $entityType
     * @param  int $attributeSetId
     * @param  int $storeId
     * @return Data\Eav\AttributeMetadata[]
     */
    public function getAllAttributeSetMetadata($entityType, $attributeSetId = 0, $storeId = null);

    /**
     * Retrieve Attribute Metadata
     *
     * @param  string $entityType
     * @param  string $attributeCode
     * @return Data\Eav\AttributeMetadata
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAttributeMetadata($entityType, $attributeCode);
}
