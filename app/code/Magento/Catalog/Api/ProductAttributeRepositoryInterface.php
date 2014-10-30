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
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Framework\Data\Search\SearchResultsInterface
     * @see \Magento\Catalog\Service\V1\MetadataServiceInterface::getAllAttributeMetadata
     */
    public function getList(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);

    /**
     * Retrieve specific attribute
     *
     * @param string $attributeCode
     * @return \Magento\Catalog\Api\Data\ProductAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Magento\Catalog\Service\V1\MetadataServiceInterface::getAttributeMetadata
     */
    public function get($attributeCode);

    /**
     * Save attribute data
     *
     * @param \Magento\Catalog\Api\Data\ProductAttributeInterface $attribute
     * @return \Magento\Catalog\Api\Data\ProductAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function save(\Magento\Catalog\Api\Data\ProductAttributeInterface $attribute);

    /**
     * Delete Attribute
     *
     * @param \Magento\Catalog\Api\Data\ProductAttributeInterface $attribute
     * @return bool True if the entity was deleted (always true)
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function delete(\Magento\Catalog\Api\Data\ProductAttributeInterface $attribute);

    /**
     * Delete Attribute by id
     *
     * @param string $attributeCode
     * @return true
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($attributeCode);
}
