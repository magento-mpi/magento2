<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product;

/**
 * Class Product MetadataServiceInterface
 */
interface MetadataServiceInterface extends \Magento\Framework\Api\MetadataServiceInterface
{
    /**#@+
     * Predefined constants
     */
    const ENTITY_TYPE = 'catalog_product';

    const DEFAULT_ATTRIBUTE_SET_ID = 4;

    const DATA_OBJECT_CLASS_NAME = 'Magento\Catalog\Service\V1\Data\Product';
    /**#@-*/

    /**
     * Retrieve EAV attribute metadata of product
     *
     * @param int $attributeSetId
     * @return \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata[]
     */
    public function getProductAttributesMetadata($attributeSetId = self::DEFAULT_ATTRIBUTE_SET_ID);

    /**
     * {@inheritdoc}
     */
    public function getCustomAttributesMetadata($dataObjectClassName = self::DATA_OBJECT_CLASS_NAME);
}
