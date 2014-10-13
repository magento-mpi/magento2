<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api;

interface AttributeGroupManagementInterface 
{
    /**
     * Create attribute group
     *
     * @param string $entityType
     * @param string $attributeSetId
     * @param \Magento\Eav\Api\Data\AttributeGroupInterface $groupData
     * @return \Magento\Catalog\Service\V1\Data\Eav\AttributeGroup
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Magento\Catalog\Service\V1\Product\AttributeGroup\WriteServiceInterface::create
     */
    public function create(
        $entityType,
        $attributeSetId,
        \Magento\Eav\Api\Data\AttributeGroupInterface $groupData
    );

    /**
     * Update attribute group
     *
     * @param string $entityType
     * @param string $attributeSetId
     * @param string $groupId
     * @param \Magento\Eav\Api\Data\AttributeGroupInterface $groupData
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     * @see \Magento\Catalog\Service\V1\Product\AttributeGroup\WriteServiceInterface::update
     */
    public function update(
        $entityType,
        $attributeSetId,
        $groupId,
        \Magento\Eav\Api\Data\AttributeGroupInterface $groupData
    );

    /**
     * Remove attribute group
     *
     * @param string $entityType
     * @param string $attributeSetId
     * @param string $groupId
     * @return bool
     * @see \Magento\Catalog\Service\V1\Product\AttributeGroup\WriteServiceInterface::delete
     */
    public function remove($entityType, $attributeSetId, $groupId);
}
