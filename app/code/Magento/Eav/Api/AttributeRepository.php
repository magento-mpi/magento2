<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api;

interface AttributeRepository
{
    /**
     * @param Attribute $attribute
     */
    public function persist(\Magento\Eav\Api\Attribute $attribute);

    /**
     * @param string $id
     */
    public function delete($id);

    /**
     * Retrieve all attributes for entityType filtered by form code
     *
     * @param string $searchCriteria
     * @return \Magento\Eav\Api\Attribute[]
     */
    public function getList($searchCriteria);

    /**
     * @param $entityTypeCode
     * @return \Magento\Eav\Api\Attribute[]
     */
    public function getByType($entityTypeCode);

    /**
     * Retrieve Customer Addresses EAV attribute metadata
     *
     * @param string $attributeCode
     * @return \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($attributeCode);
}
