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
     */
    public function save(\Magento\Eav\Api\Data\AttributeGroupInterface $group);

    /**
     * Retrieve list of attribute groups
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Eav\Api\Data\AttributeGroupInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Magento\Catalog\Service\V1\Product\AttributeGroup\ReadServiceInterface::getList
     */
    public function getList(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);

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
     * @param string $groupId
     * @return bool
     */
    public function delete($groupId);
}
