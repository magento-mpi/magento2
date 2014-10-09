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
     */
    public function createSet(\Magento\Catalog\Api\Data\AttributeSetInterface $attributeSet, $skeletonId);

    /**
     * Update attribute set from data
     *
     * @param \Magento\Catalog\Api\Data\AttributeSetInterface $attributeSet
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function updateSet(\Magento\Catalog\Api\Data\AttributeSetInterface $attributeSet);

    /**
     * @param int $attributeSetId
     * @param \Magento\Catalog\Api\Data\AttributeInterface $data
     * @return int
     */
    public function addToAttributeSet($attributeSetId, \Magento\Catalog\Api\Data\AttributeInterface $data);

    /**
     * Remove attribute from attribute set
     *
     * @param string $attributeSetId
     * @param string $attributeId
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @return bool
     */
    public function removeFromAttributeSet($attributeSetId, $attributeId);

    /**
     * Retrieve related attributes based on given attribute set ID
     *
     * @param int $attributeSetId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $attributeSetId is not found
     * @return \Magento\Catalog\Api\Data\AttributeInterface[]
     */
    public function getAttributeList($attributeSetId);

    /**
     * Retrieve the list of media attributes (fronted input type is media_image) assigned to the given attribute set.
     *
     * @param int $attributeSetId
     * @return \Magento\Catalog\Api\Data\AttributeInterface[]
     */
    public function getMediaAttributes($attributeSetId);
}
