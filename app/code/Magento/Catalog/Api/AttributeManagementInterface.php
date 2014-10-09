<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api;

interface AttributeManagementInterface
{
    /**
     * Create attribute data
     *
     * @param \Magento\Catalog\Api\Data\Product\Attribute\AttributeMetadataInterface $attributeMetadata
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Eav\Exception from validate()
     */
    public function create(Data\Product\Attribute\AttributeMetadataInterface $attributeMetadata);

    /**
     * Update product attribute process
     *
     * @param  string $id
     * @param  \Magento\Catalog\Api\Data\Product\Attribute\AttributeMetadataInterface $attributeMetadata
     * @return string
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function update(Data\Product\Attribute\AttributeMetadataInterface $attributeMetadata);

    /**
     * Retrieve list of product attribute types
     *
     * @return \Magento\Catalog\Api\Data\Product\Attribute\AttributeTypeInterface[]
     */
    public function getAttributeTypes();
}
