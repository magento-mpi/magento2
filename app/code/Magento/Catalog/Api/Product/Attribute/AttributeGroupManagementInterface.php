<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Product\Attribute;

interface AttributeGroupManagementInterface 
{
    /**
     * Create attribute group
     *
     * @param string $attributeSetId
     * @param \Magento\Catalog\Api\Data\Product\Attribute\AttributeGroupInterface $groupData
     * @return \Magento\Catalog\Service\V1\Data\Eav\AttributeGroup
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function create(
        $attributeSetId,
        \Magento\Catalog\Api\Data\Product\Attribute\AttributeGroupInterface $groupData
    );

    /**
     * Update attribute group
     *
     * @param string $attributeSetId
     * @param string $groupId
     * @param \Magento\Catalog\Api\Data\Product\Attribute\AttributeGroupInterface $groupData
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     */
    public function update(
        $attributeSetId,
        $groupId,
        \Magento\Catalog\Api\Data\Product\Attribute\AttributeGroupInterface $groupData
    );

    /**
     * Remove attribute group
     *
     * @param string $attributeSetId
     * @param string $groupId
     * @return bool
     */
    public function delete($attributeSetId, $groupId);
}
 