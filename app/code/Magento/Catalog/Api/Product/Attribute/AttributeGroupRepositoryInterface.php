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
 * @todo Create new model \Magento\Catalog\Model\Product\Attribute\AttributeGroupRepository
X
 */
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
     * @see \Magento\Catalog\Service\V1\Product\AttributeGroup\ReadServiceInterface::getList
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
