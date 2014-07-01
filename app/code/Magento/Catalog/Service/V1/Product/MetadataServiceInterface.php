<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product;


use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata;
/**
 * Class Product MetadataServiceInterface
 */
interface MetadataServiceInterface
{
    /**#@+
     * Predefined constants
     */
    const ENTITY_TYPE = 'catalog_product';

    const DEFAULT_ATTRIBUTE_SET_ID = 4;
    /**#@-*/

    /**
     * Retrieve custom EAV attribute metadata of product
     *
     * @return \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata[]
     */
    public function getCustomAttributesMetadata($attributeSetId = self::DEFAULT_ATTRIBUTE_SET_ID);

    /**
     * Retrieve EAV attribute metadata of product
     *
     * @return \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata[]
     */
    public function getProductAttributesMetadata($attributeSetId = self::DEFAULT_ATTRIBUTE_SET_ID);
}
