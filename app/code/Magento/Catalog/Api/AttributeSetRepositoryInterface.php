<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api;

/**
 * Interface AttributeSetRepository must be implemented in new model AttributeSetRepository
 */
interface AttributeSetRepositoryInterface
{
    /**
     * Create attribute set from data
     *
     * @param \Magento\Catalog\Api\Data\AttributeSet $attributeSet
     * @param int $skeletonId
     * @return int
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function create(\Magento\Catalog\Api\Data\AttributeSet $attributeSet, $skeletonId);

    /**
     * Update attribute set data
     *
     * @param \Magento\Catalog\Api\Data\AttributeSet $attributeSetData
     * @return int attribute set ID
     * @throws \Magento\Framework\Model\Exception If attribute set is not found
     */
    public function update(\Magento\Catalog\Api\Data\AttributeSet $attributeSetData);

    /**
     * Retrieve list of Attribute Sets
     *
     * @return \Magento\Catalog\Api\Data\AttributeSet[]
     */
    public function getList();

    /**
     * Retrieve attribute set information based on given ID
     *
     * @param int $attributeSetId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $attributeSetId is not found
     * @return \Magento\Catalog\Api\Data\AttributeSet
     */
    public function get($attributeSetId);

    /**
     * Remove attribute set by id
     *
     * @param int $attributeSetId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @return bool
     */
    public function delete($attributeSetId);
}
