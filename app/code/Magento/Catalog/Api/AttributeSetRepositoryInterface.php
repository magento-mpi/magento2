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
 * Interface AttributeSetRepositoryInterface must be implemented in
 * new model \Magento\Catalog\Model\AttributeSetRepository
 */
interface AttributeSetRepositoryInterface
{
    /**
     * Save attribute set data
     *
     * @param \Magento\Catalog\Api\Data\AttributeSetInterface $attributeSetData
     * @return int attribute set ID
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Model\Exception If attribute set is not found
     */
    public function save(\Magento\Catalog\Api\Data\AttributeSetInterface $attributeSetData);

    /**
     * Retrieve list of Attribute Sets
     *
     * @return \Magento\Catalog\Api\Data\AttributeSetInterface[]
     */
    public function getList();

    /**
     * Retrieve attribute set information based on given ID
     *
     * @param int $attributeSetId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $attributeSetId is not found
     * @return \Magento\Catalog\Api\Data\AttributeSetInterface
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
    public function remove($attributeSetId);
}
