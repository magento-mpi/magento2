<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category;

use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata;

/**
 * Class Category MetadataServiceInterface
 */
interface MetadataServiceInterface extends \Magento\Framework\Service\Data\MetadataServiceInterface
{
    /**#@+
     * Predefined constants
     */
    const ENTITY_TYPE = 'catalog_category';

    const DEFAULT_ATTRIBUTE_SET_ID = 3;

    const DATA_OBJECT_CLASS_NAME = 'Magento\Catalog\Service\V1\Data\Category';
    /**#@-*/

    /**
     * Retrieve EAV attribute metadata of category
     *
     * @param int $attributeSetId
     * @return AttributeMetadata[]
     */
    public function getCategoryAttributesMetadata($attributeSetId = self::DEFAULT_ATTRIBUTE_SET_ID);

    /**
     * {@inheritdoc}
     */
    public function getCustomAttributesMetadata($dataObjectClassName = self::DATA_OBJECT_CLASS_NAME);
}
