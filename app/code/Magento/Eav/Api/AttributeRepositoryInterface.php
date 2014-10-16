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
     * @param \Magento\Framework\Data\Search\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Eav\Api\Data\AttributeInterface[]
     * @see \Magento\Catalog\Service\V1\MetadataServiceInterface::getAllAttributeMetadata
     */
    public function getList(\Magento\Framework\Data\Search\SearchCriteriaInterface $searchCriteria);


    /**
     * Retrieve specific attribute
     *
     * @param \Magento\Eav\Api\Data\AttributeIdentifierInterface $identifier
     * @return \Magento\Eav\Api\Data\AttributeInterface
     * @see \Magento\Catalog\Service\V1\MetadataServiceInterface::getAttributeMetadata
     */
    public function get(\Magento\Eav\Api\Data\AttributeIdentifierInterface $identifier);

    /**
     * Create attribute data
     *
     * @param \Magento\Eav\Api\Data\AttributeInterface $attributeMetadata
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Eav\Exception from validate()
     */
    public function save(\Magento\Eav\Api\Data\AttributeInterface $attribute);

    /**
     * Delete Attribute
     *
     * @param Data\AttributeInterface $attribute
     * @return bool True if the entity was deleted (always true)
     */
    public function delete(\Magento\Eav\Api\Data\AttributeInterface $attribute);
}
