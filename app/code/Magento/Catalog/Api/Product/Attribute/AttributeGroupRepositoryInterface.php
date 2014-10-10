<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Product\Attribute;

interface AttributeGroupRepositoryInterface 
{
    /**
     * Save attribute group
     *
     * @param \Magento\Catalog\Api\Data\Product\Attribute\AttributeGroupInterface $group
     * @return \Magento\Catalog\Api\Data\Product\Attribute\AttributeGroupInterface
     */
    public function save(\Magento\Catalog\Api\Data\Product\Attribute\AttributeGroupInterface $group);

    /**
     * Retrieve list of attribute groups
     *
     * @param string $attributeSetId
     * @return \Magento\Catalog\Api\Data\Product\Attribute\AttributeGroupInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList($attributeSetId);

    /**
     * Remove attribute group
     *
     * @param string $groupId
     * @return bool
     */
    public function delete($groupId);
}
