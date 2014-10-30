<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api;

interface AttributeManagementInterface 
{
    /**
     * @param string $entityTypeCode
     * @param int $attributeSetId
     * @param int $attributeGroupId
     * @param string $attributeCode
     * @param int $sortOrder
     * @return int
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Magento\Catalog\Service\V1\Product\AttributeSet\AttributeServiceInterface::addAttribute
     */
    public function assign($entityTypeCode, $attributeSetId, $attributeGroupId, $attributeCode, $sortOrder);

    /**
     * Remove attribute from attribute set
     *
     * @param string $attributeSetId
     * @param string $attributeCode
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @return bool
     * @see \Magento\Catalog\Service\V1\Product\AttributeSet\AttributeServiceInterface::deleteAttribute
     */
    public function unassign($attributeSetId, $attributeCode);

    /**
     * Retrieve related attributes based on given attribute set ID
     *
     * @param string $entityTypeCode
     * @param string $attributeSetId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $attributeSetId is not found
     * @return \Magento\Eav\Api\Data\AttributeInterface[]
     * @see \Magento\Catalog\Service\V1\Product\AttributeSet\ReadServiceInterface::getAttributeList
     */
    public function getAttributes($entityTypeCode, $attributeSetId);
}
