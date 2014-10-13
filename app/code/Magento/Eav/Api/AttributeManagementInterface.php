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
     * @param $entityType
     * @param $attributeSetName
     * @param $attributeGroup
     * @param $attributeCode
     * @param $sortOrder
     * @return int
     * @see \Magento\Catalog\Service\V1\Product\AttributeSet\AttributeServiceInterface::addAttribute
     */
    public function assign($entityType, $attributeSetName, $attributeGroup, $attributeCode, $sortOrder);

    /**
     * Remove attribute from attribute set
     *
     * @param string $attributeSetName
     * @param string $attributeCode
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @return bool
     * @see \Magento\Catalog\Service\V1\Product\AttributeSet\AttributeServiceInterface::deleteAttribute
     */
    public function unassign($attributeSetName, $attributeCode);

    /**
     * Retrieve related attributes based on given attribute set ID
     *
     * @param string $entityType
     * @param string $attributeSetName
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $attributeSetId is not found
     * @return \Magento\Catalog\Api\Data\AttributeInterface[]
     * @see \Magento\Catalog\Service\V1\Product\AttributeSet\ReadServiceInterface::getAttributeList
     */
    public function getAttributes($entityType, $attributeSetName);
}
 