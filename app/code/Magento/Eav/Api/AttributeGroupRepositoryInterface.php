<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api;

interface AttributeGroupRepositoryInterface 
{
    /**
     * Save attribute group
     *
     * @param \Magento\Eav\Api\Data\AttributeGroupInterface $group
     * @return \Magento\Eav\Api\Data\AttributeGroupInterface
     * @see \Magento\Catalog\Service\V1\Product\AttributeGroup\WriteServiceInterface::update
     * @see \Magento\Catalog\Service\V1\Product\AttributeGroup\WriteServiceInterface::create
     */
    public function save(\Magento\Eav\Api\Data\AttributeGroupInterface $group);

    /**
     * Retrieve list of attribute groups
     *
     * @param \Magento\Framework\Data\Search\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Eav\Api\Data\AttributeGroupInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Magento\Catalog\Service\V1\Product\AttributeGroup\ReadServiceInterface::getList
     */
    public function getList(\Magento\Framework\Data\Search\SearchCriteriaInterface $searchCriteria);

    /**
     * Retrieve attribute group
     *
     * @param int $groupId
     * @return \Magento\Eav\Api\Data\AttributeGroupInterface
     */
    public function get($groupId);

    /**
     * Remove attribute group
     *
     * @param \Magento\Eav\Api\Data\AttributeGroupInterface $group
     * @return bool
     * @see \Magento\Catalog\Service\V1\Product\AttributeGroup\WriteServiceInterface::delete
     */
    public function delete(\Magento\Eav\Api\Data\AttributeGroupInterface $group);

    /**
     * Remove attribute group by id
     *
     * @param $groupId
     * @return bool
     */
    public function deleteById($groupId);
}
