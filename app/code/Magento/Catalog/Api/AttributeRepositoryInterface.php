<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api;

interface AttributeRepositoryInterface
{
    /**
     * Create attribute from data
     *
     * @param \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata $attributeMetadata
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Eav\Exception from validate()
     */
    public function create(\Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata $attributeMetadata);

    /**
     * Update product attribute process
     *
     * @param  string $id
     * @param  \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata $attribute
     * @return string
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function update($id, \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata $attribute);

    /**
     * Get full information about a required attribute with the list of options
     *
     * @param  string $id
     * @return \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * Retrieve the list of product attributes
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Catalog\Service\V1\Data\Product\Attribute\SearchResults containing Data\Eav\Attribute objects
     */
    public function getList(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);

    /**
     * Delete Attribute
     *
     * @param  string $attributeId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID is not found
     * @throws \Exception If something goes wrong during delete
     * @return bool True if the entity was deleted (always true)
     */
    public function delete($attributeId);
}
