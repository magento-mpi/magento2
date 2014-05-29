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
     * @param \Magento\Catalog\Service\V1\Product\Data\Eav\AttributeGroup $groupData
     * @return \Magento\Catalog\Service\V1\Product\Data\Eav\AttributeGroup
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function create($attributeSetId, \Magento\Catalog\Service\V1\Product\Data\Eav\AttributeGroup $groupData);

    /**
     * Update attribute group
     *
     * @param string $groupId
     * @param \Magento\Catalog\Service\V1\Product\Data\Eav\AttributeGroup $groupData
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     */
    public function update($groupId, \Magento\Catalog\Service\V1\Product\Data\Eav\AttributeGroup $groupData);

    /**
     * Remove attribute group
     *
     * @param string $groupId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @return bool
     */
    public function delete($groupId);
}
