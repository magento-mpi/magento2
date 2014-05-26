<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\AttributeGroup;

interface ReadServiceInterface
{
    /**
     * @param string $attributeSetId
     * @return \Magento\Catalog\Service\V1\Product\Data\Eav\AttributeGroup[]
     */
    public function getList($attributeSetId);
}
