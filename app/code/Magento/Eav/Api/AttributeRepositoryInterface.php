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
     * @param array $arguments
     * @return \Magento\Framework\Data\Search\SearchResultsInterface
     * @see \Magento\Catalog\Service\V1\MetadataServiceInterface::getAllAttributeMetadata
     */
    public function getList(
        \Magento\Framework\Data\Search\SearchCriteriaInterface $searchCriteria,
        array $arguments = []
    );

    /**
     * Retrieve specific attribute
     *
     * @param \Magento\Eav\Api\Data\AttributeIdentifierInterface $identifier
     * @param array $arguments
     * @return \Magento\Eav\Api\Data\AttributeInterface
     * @see \Magento\Catalog\Service\V1\MetadataServiceInterface::getAttributeMetadata
     */
    public function get(\Magento\Eav\Api\Data\AttributeIdentifierInterface $identifier, array $arguments = []);

    /**
     * Create attribute data
     *
     * @param \Magento\Eav\Api\Data\AttributeInterface $attributeMetadata
     * @param array $arguments
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Eav\Exception from validate()
     */
    public function save(\Magento\Eav\Api\Data\AttributeInterface $attribute, array $arguments = []);

    /**
     * Delete Attribute
     *
     * @param Data\AttributeInterface $attribute
     * @param array $arguments
     * @return bool True if the entity was deleted (always true)
     */
    public function delete(\Magento\Eav\Api\Data\AttributeInterface $attribute, array $arguments = []);
}
