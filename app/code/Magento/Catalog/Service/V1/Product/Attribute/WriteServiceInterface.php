<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute;

/**
 * Class WriteServiceInterface
 * @package Magento\Catalog\Service\V1\Product\Attribute
 */
interface WriteServiceInterface
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
     * Delete Attribute
     *
     * @param  string $attributeId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID is not found
     * @throws \Exception If something goes wrong during delete
     * @return bool True if the entity was deleted (always true)
     */
    public function remove($attributeId);
}
