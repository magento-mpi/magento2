<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Category\Attribute;

/**
 * Interface MetadataRepositoryInterface
 * @see \Magento\Catalog\Service\V1\Category\MetadataServiceInterface
 */
interface MetadataRepositoryInterface extends \Magento\Eav\Api\Entity\Attribute\MetadataRepositoryInterface
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
     * @return \Magento\Catalog\Api\Data\Category\Attribute\MetadataInterface[]
     */
    public function getCategoryAttributesMetadata($attributeSetId = self::DEFAULT_ATTRIBUTE_SET_ID);

    /**
     * @param string $dataObjectClassName
     * @return mixed
     */
    public function getCustomAttributesMetadata($dataObjectClassName = self::DATA_OBJECT_CLASS_NAME);
}
