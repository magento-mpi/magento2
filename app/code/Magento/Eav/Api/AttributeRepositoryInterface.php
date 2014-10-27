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
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @param string $entityTypeCode
     * @return \Magento\Framework\Data\Search\SearchResultsInterface
     * @see \Magento\Catalog\Service\V1\MetadataServiceInterface::getAllAttributeMetadata
     */
    public function getList(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria, $entityTypeCode);

    /**
     * Retrieve specific attribute
     *
     * @param \Magento\Eav\Model\Entity\Attribute\Identifier $identifier
     * @return \Magento\Eav\Api\Data\AttributeInterface
     * @see \Magento\Catalog\Service\V1\MetadataServiceInterface::getAttributeMetadata
     */
    public function get(\Magento\Eav\Model\Entity\Attribute\Identifier $identifier);

    /**
     * Create attribute data
     *
     * @param \Magento\Eav\Api\Data\AttributeInterface $attribute
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Eav\Exception from validate()
     */
    public function save(\Magento\Eav\Api\Data\AttributeInterface $attribute);

    /**
     * Delete Attribute
     *
     * @param Data\AttributeInterface $attribute
     * @return bool True if the entity was deleted
     */
    public function delete(Data\AttributeInterface $attribute);

    /**
     * Delete Attribute By Id
     *
     * @param $attributeId
     * @return bool True if the entity was deleted
     */
    public function deleteById($attributeId);
}
