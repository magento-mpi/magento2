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
     * @param int $attributeSetId
     * @param int $storeId
     * @return \Magento\Eav\Api\Data\AttributeInterface[]
     * @see \Magento\Catalog\Service\V1\MetadataServiceInterface::getAllAttributeMetadata
     */
    public function getList($entityTypeCode, $attributeSetId = 0, $storeId = null);


    /**
     * Retrieve specific attribute
     *
     * @param string $entityTypeCode
     * @param string $attributeCode
     * @return \Magento\Eav\Api\Data\AttributeInterface
     * @see \Magento\Catalog\Service\V1\MetadataServiceInterface::getAttributeMetadata
     */
    public function get($entityTypeCode, $attributeCode);

    /**
     * Create attribute data
     *
     * @param \Magento\Eav\Api\Data\AttributeInterface $attributeMetadata
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Eav\Exception from validate()
     */
    public function save(\Magento\Eav\Api\Data\AttributeInterface $attributeMetadata);

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
