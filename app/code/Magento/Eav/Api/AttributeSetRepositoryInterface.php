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
     * @param array $arguments
     * @return int attribute set ID
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Model\Exception If attribute set is not found
     * @see \Magento\Catalog\Service\V1\Product\AttributeSet\WriteServiceInterface::update
     */
    public function save(\Magento\Eav\Api\Data\AttributeSetInterface $attributeSet, array $arguments = []);

    /**
     * Retrieve list of Attribute Sets
     *
     * @param \Magento\Framework\Data\Search\SearchCriteriaInterface $searchCriteria
     * @param array $arguments
     * @return \Magento\Framework\Data\Search\SearchResultsInterface
     * @see \Magento\Catalog\Service\V1\Product\AttributeSet\ReadServiceInterface::getList
     */
    public function getList(SearchCriteriaInterface $searchCriteria, array $arguments = []);

    /**
     * Retrieve attribute set information based on given ID
     *
     * @param int $attributeSetId
     * @param array $arguments
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $attributeSetId is not found
     * @return \Magento\Eav\Api\Data\AttributeSetInterface
     * @see \Magento\Catalog\Service\V1\Product\AttributeSet\ReadServiceInterface::getInfo
     */
    public function get($attributeSetId, array $arguments = []);

    /**
     * Remove attribute set by id
     *
     * @param \Magento\Eav\Api\Data\AttributeSetInterface $attributeSet
     * @param array $arguments
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @return bool
     * @see \Magento\Catalog\Service\V1\Product\AttributeSet\WriteServiceInterface::remove
     */
    public function delete(\Magento\Eav\Api\Data\AttributeSetInterface $attributeSet, array $arguments = []);
}
