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
 * Interface AttributeSetManagement must be implemented
 * in new model \Magento\Catalog\Model\AttributeSetManagement
 */
interface AttributeSetManagementInterface
{
    /**
     * Create attribute set from data
     *
     * @param \Magento\Catalog\Api\Data\AttributeSetInterface $attributeSet
     * @param int $skeletonId
     * @return int
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Magento\Catalog\Service\V1\Product\AttributeSet\WriteServiceInterface::create
     */
    public function create(\Magento\Catalog\Api\Data\AttributeSetInterface $attributeSet, $skeletonId);

    /**
     * Update attribute set from data
     *
     * @param \Magento\Catalog\Api\Data\AttributeSetInterface $attributeSet
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     *
     * @see \Magento\Catalog\Service\V1\Product\AttributeSet\WriteServiceInterface::update
     * @todo remove this method. use \Magento\Catalog\Api\Product\Attribute\AttributeSetRepositoryInterface::save
     */
    public function update(\Magento\Catalog\Api\Data\AttributeSetInterface $attributeSet);

    /**
     * @param int $attributeSetId
     * @param \Magento\Catalog\Api\Data\AttributeInterface $data
     * @return int
     * @todo move this method to \Magento\Eav\Api\AttributeManagementInterface::assign
     * @todo use \Magento\Eav\Api\Data\AttributeSet\AttributeInterface instead of \Magento\Catalog\Api\Data\AttributeInterface
     */
    public function assignAttribute($entityType, $attributeSetName, $attributeGroup, $attributeCode, $sortOrder);

    /**
     * Remove attribute from attribute set
     *
     * @param string $attributeSetId
     * @param string $attributeId
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @return bool
     * @todo move this method to AttributeManagementInterface::unassign($attributeSetName, $attributeCode)
     */
    public function unAssignAttribute($attributeSetId, $attributeId);

    /**
     * Retrieve related attributes based on given attribute set ID
     *
     * @param int $attributeSetId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $attributeSetId is not found
     * @return \Magento\Catalog\Api\Data\AttributeInterface[]
     */
    public function getAttributes($entityType, $attributeSetName);
}
