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
 * Interface ProductAttributeSetRepositoryInterface must be implemented in
 * new model \Magento\Catalog\Model\AttributeSetRepository
 */
interface ProductAttributeSetRepositoryInterface
{
    /**
     * Save attribute set data
     *
     * @param \Magento\Catalog\Api\Data\AttributeSetInterface $attributeSetData
     * @return int attribute set ID
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Model\Exception If attribute set is not found
     * @see \Magento\Catalog\Service\V1\Product\AttributeSet\WriteServiceInterface::update
     */
    public function save(\Magento\Catalog\Api\Data\AttributeSetInterface $attributeSetData);

    /**
     * Retrieve list of Attribute Sets
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Catalog\Api\Data\AttributeSetInterface[]
     * @see \Magento\Catalog\Service\V1\Product\AttributeSet\ReadServiceInterface::getList
     */
    public function getList(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);

    /**
     * Retrieve attribute set information based on given ID
     *
     * @param int $attributeSetId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $attributeSetId is not found
     * @return \Magento\Catalog\Api\Data\AttributeSetInterface
     * @see \Magento\Catalog\Service\V1\Product\AttributeSet\ReadServiceInterface::getInfo
     */
    public function get($attributeSetId);

    /**
     * Remove attribute set by id
     *
     * @param int $attributeSetId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @return bool
     * @see \Magento\Catalog\Service\V1\Product\AttributeSet\WriteServiceInterface::remove
     */
    public function delete($attributeSetId);
}
