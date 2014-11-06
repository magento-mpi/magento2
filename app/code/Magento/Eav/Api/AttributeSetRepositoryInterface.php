<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api;

use \Magento\Framework\Data\Search\SearchCriteriaInterface;

interface AttributeSetRepositoryInterface
{
    /**
     * Save attribute set data
     *
     * @param \Magento\Eav\Api\Data\AttributeSetInterface $attributeSet
     * @return \Magento\Eav\Api\Data\AttributeSetInterface saved attribute set
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Model\Exception If attribute set is not found
     * @see \Magento\Catalog\Service\V1\Product\AttributeSet\WriteServiceInterface::update
     */
    public function save(\Magento\Eav\Api\Data\AttributeSetInterface $attributeSet);

    /**
     * Retrieve list of Attribute Sets
     *
     * @param \Magento\Framework\Data\Search\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Data\Search\SearchResultsInterface
     * @see \Magento\Catalog\Service\V1\Product\AttributeSet\ReadServiceInterface::getList
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Retrieve attribute set information based on given ID
     *
     * @param int $attributeSetId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $attributeSetId is not found
     * @return \Magento\Eav\Api\Data\AttributeSetInterface
     * @see \Magento\Catalog\Service\V1\Product\AttributeSet\ReadServiceInterface::getInfo
     */
    public function get($attributeSetId);

    /**
     * Remove given attribute set
     *
     * @param \Magento\Eav\Api\Data\AttributeSetInterface $attributeSet
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @return bool
     * @see \Magento\Catalog\Service\V1\Product\AttributeSet\WriteServiceInterface::remove
     */
    public function delete(\Magento\Eav\Api\Data\AttributeSetInterface $attributeSet);

    /**
     * Remove attribute set by given ID
     *
     * @param int $attributeSetId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @return bool
     * @see \Magento\Catalog\Service\V1\Product\AttributeSet\WriteServiceInterface::remove
     */
    public function deleteById($attributeSetId);
}
