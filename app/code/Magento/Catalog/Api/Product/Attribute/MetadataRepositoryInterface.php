<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Product\Attribute;

/**
 * Interface MetadataRepositoryInterface
 * @see \Magento\Catalog\Service\V1\Product\MetadataServiceInterface
 */
interface MetadataRepositoryInterface extends \Magento\Eav\Api\Entity\Attribute\MetadataRepositoryInterface
{
    /**#@+
     * Predefined constants
     */
    const ENTITY_TYPE = 'catalog_product';

    const DEFAULT_ATTRIBUTE_SET_ID = 4;

    const DATA_OBJECT_CLASS_NAME = 'Magento\Catalog\Service\V1\Data\Product';
    /**#@-*/

    /**
     * Retrieve EAV attribute metadata of product
     *
     * @param int $attributeSetId
     * @return \Magento\Catalog\Api\Data\Product\Attribute\MetadataInterface[]
     */
    public function getProductAttributesMetadata($attributeSetId = self::DEFAULT_ATTRIBUTE_SET_ID);

    /**
     * @param string $dataObjectClassName
     * @return mixed
     */
    public function getCustomAttributesMetadata($dataObjectClassName = self::DATA_OBJECT_CLASS_NAME);

    /**
     * @todo: maybe move create and update methods to EAV attribute MetadataRepository (currently category attributes cannot be created by user)
     * Create attribute data
     *
     * @param \Magento\Catalog\Api\Data\Product\Attribute\MetadataInterface $attributeMetadata
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Eav\Exception from validate()
     */
    public function create(\Magento\Catalog\Api\Data\Product\Attribute\MetadataInterface $attributeMetadata);

    /**
     * Update product attribute process
     *
     * @param  \Magento\Catalog\Api\Data\Product\Attribute\MetadataInterface $attributeMetadata
     * @return string
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function update(\Magento\Catalog\Api\Data\Product\Attribute\MetadataInterface $attributeMetadata);
}
