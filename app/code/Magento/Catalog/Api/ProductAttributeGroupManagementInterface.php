<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Product\Attribute;

/**
 * Created from:
 * @see \Magento\Catalog\Service\V1\Product\AttributeGroup\WriteServiceInterface
 */
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
     * @see \Magento\Catalog\Service\V1\Product\AttributeGroup\WriteServiceInterface::create
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
     * @see \Magento\Catalog\Service\V1\Product\AttributeGroup\WriteServiceInterface::update
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
     * @see \Magento\Catalog\Service\V1\Product\AttributeGroup\WriteServiceInterface::delete
     */
    public function delete($attributeSetId, $groupId);
}
