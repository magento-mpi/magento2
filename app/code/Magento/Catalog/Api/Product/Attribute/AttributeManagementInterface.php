<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Product\Attribute;

interface AttributeManagementInterface
{
    /**
     * Create attribute data
     *
     * @param \Magento\Catalog\Api\Data\Eav\AttributeMetadataInterface $attributeMetadata
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Eav\Exception from validate()
     */
    public function create(\Magento\Catalog\Api\Data\Eav\AttributeMetadataInterface $attributeMetadata);

    /**
     * Update product attribute process
     *
     * @param  \Magento\Catalog\Api\Data\Eav\AttributeMetadataInterface $attributeMetadata
     * @return string
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function update(\Magento\Catalog\Api\Data\Eav\AttributeMetadataInterface $attributeMetadata);

    /**
     * Retrieve list of product attribute types
     *
     * @return \Magento\Catalog\Api\Data\Product\Attribute\AttributeTypeInterface[]
     */
    public function getAttributeTypes();
}
