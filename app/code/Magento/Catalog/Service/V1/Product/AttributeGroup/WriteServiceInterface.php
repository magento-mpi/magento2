<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\AttributeGroup;

interface WriteServiceInterface
{
    /**
     * Create attribute group
     *
     * @param string $attributeSetId
     * @param \Magento\Catalog\Service\V1\Data\Eav\AttributeGroup $groupData
     * @return \Magento\Catalog\Service\V1\Data\Eav\AttributeGroup
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @deprecated
     * @see \Magento\Eav\Api\AttributeGroupManagementInterface::create
     */
    public function create($attributeSetId, \Magento\Catalog\Service\V1\Data\Eav\AttributeGroup $groupData);

    /**
     * Update attribute group
     *
     * @param string $attributeSetId
     * @param string $groupId
     * @param \Magento\Catalog\Service\V1\Data\Eav\AttributeGroup $groupData
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     * @deprecated
     * @see \Magento\Eav\Api\AttributeGroupManagementInterface::update
     */
    public function update($attributeSetId, $groupId, \Magento\Catalog\Service\V1\Data\Eav\AttributeGroup $groupData);

    /**
     * Remove attribute group
     *
     * @param string $attributeSetId
     * @param string $groupId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @return bool
     * @deprecated
     * @see \Magento\Eav\Api\AttributeGroupManagementInterface::remove
     */
    public function delete($attributeSetId, $groupId);
}
