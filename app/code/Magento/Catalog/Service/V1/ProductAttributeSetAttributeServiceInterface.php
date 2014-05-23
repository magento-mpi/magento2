<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

interface ProductAttributeSetAttributeServiceInterface
{
    /**
     * @param int $attributeSetId
     * @param \Magento\Catalog\Service\V1\Data\Eav\AttributeSet\Attribute $data
     * @return int
     */
    public function addAttribute($attributeSetId, \Magento\Catalog\Service\V1\Data\Eav\AttributeSet\Attribute $data);
}
