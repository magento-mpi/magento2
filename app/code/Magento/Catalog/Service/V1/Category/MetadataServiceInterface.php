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
interface MetadataServiceInterface
{
    /**#@+
     * Predefined constants
     */
    const ENTITY_TYPE = 'catalog_category';

    const DEFAULT_ATTRIBUTE_SET_ID = 3;
    /**#@-*/

    /**
     * Retrieve custom EAV attribute metadata of category
     *
     * @return AttributeMetadata[]
     */
    public function getCustomAttributesMetadata($attributeSetIds);

    /**
     * Retrieve EAV attribute metadata of category
     *
     * @return AttributeMetadata[]
     */
    public function getCategoryAttributesMetadata($attributeSetId = self::DEFAULT_ATTRIBUTE_SET_ID);
}