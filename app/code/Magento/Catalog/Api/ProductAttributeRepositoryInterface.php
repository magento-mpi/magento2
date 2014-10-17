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
 * Interface RepositoryInterface must be implemented in new model
 */
interface ProductAttributeRepositoryInterface
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
     * @param string $identifier
     * @param array $arguments
     * @return \Magento\Catalog\Api\Data\ProductAttributeInterface
     * @see \Magento\Catalog\Service\V1\MetadataServiceInterface::getAttributeMetadata
     */
    public function get($identifier, array $arguments = []);

    /**
     * Create attribute data
     *
     * @param \Magento\Catalog\Api\Data\ProductAttributeInterface $attribute
     * @param array $arguments
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Eav\Exception from validate()
     */
    public function save(\Magento\Catalog\Api\Data\ProductAttributeInterface $attribute, array $arguments = []);

    /**
     * Delete Attribute
     *
     * @param \Magento\Catalog\Api\Data\ProductAttributeInterface $attribute
     * @param array $arguments
     * @return bool True if the entity was deleted (always true)
     */
    public function delete(\Magento\Catalog\Api\Data\ProductAttributeInterface $attribute, array $arguments = []);
}
