<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api;

interface AttributeRepositoryInterface
{
    /**
     * Retrieve all attributes for entity type
     *
     * @param string $entityTypeCode
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Framework\Data\Search\SearchResultsInterface
     * @see \Magento\Catalog\Service\V1\MetadataServiceInterface::getAllAttributeMetadata
     */
    public function getList($entityTypeCode, \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);

    /**
     * Retrieve specific attribute
     *
     * @param string $entityTypeCode
     * @param string $attributeCode
     * @return \Magento\Eav\Api\Data\AttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Magento\Catalog\Service\V1\MetadataServiceInterface::getAttributeMetadata
     */
    public function get($entityTypeCode, $attributeCode);

    /**
     * Create attribute data
     *
     * @param \Magento\Eav\Api\Data\AttributeInterface $attribute
     * @return string
     * @throws \Magento\Framework\Exception\StateException
     */
    public function save(\Magento\Eav\Api\Data\AttributeInterface $attribute);

    /**
     * Delete Attribute
     *
     * @param Data\AttributeInterface $attribute
     * @return bool True if the entity was deleted
     * @throws \Magento\Framework\Exception\StateException
     */
    public function delete(Data\AttributeInterface $attribute);

    /**
     * Delete Attribute By Id
     *
     * @param $attributeId
     * @return bool True if the entity was deleted
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteById($attributeId);
}
