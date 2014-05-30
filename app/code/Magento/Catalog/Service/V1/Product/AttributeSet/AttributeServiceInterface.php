<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\AttributeSet;

interface AttributeServiceInterface
{
    /**
     * @param int $attributeSetId
     * @param \Magento\Catalog\Service\V1\Data\Eav\AttributeSet\Attribute $data
     * @return int
     */
    public function addAttribute($attributeSetId, \Magento\Catalog\Service\V1\Data\Eav\AttributeSet\Attribute $data);

    /**
     * Remove attribute from attribute set
     *
     * @param string $attributeSetId
     * @param string $attributeId
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return bool
     */
    public function deleteAttribute($attributeSetId, $attributeId);
}
