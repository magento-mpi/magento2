<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\Catalog\Model\Product\Attribute\Source\InputtypeFactory;

/**
 * Class ProductAttributeReadService
 * @package Magento\Catalog\Service\V1
 */
class ProductAttributeReadService implements ProductAttributeReadServiceInterface
{
    /**
     * @var ProductMetadataServiceInterface
     */
    private $metadataService;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\InputtypeFactory
     */
    private $inputTypeFactory;

    /**
     * @var Data\ProductAttributeTypeBuilder
     */
    private $attributeTypeBuilder;

    /**
     * @param ProductMetadataServiceInterface $metadataService
     * @param InputtypeFactory $inputTypeFactory
     * @param Data\ProductAttributeTypeBuilder $attributeTypeBuilder
     */
    public function __construct(
        ProductMetadataServiceInterface $metadataService,
        InputtypeFactory $inputTypeFactory,
        Data\ProductAttributeTypeBuilder $attributeTypeBuilder
    ) {
        $this->metadataService = $metadataService;
        $this->inputTypeFactory = $inputTypeFactory;
        $this->attributeTypeBuilder = $attributeTypeBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function types()
    {
        $types = [];
        $inputType = $this->inputTypeFactory->create();

        foreach ($inputType->toOptionArray() as $option) {
            $types[] = $this->attributeTypeBuilder->populateWithArray($option)->create();
        }
        return $types;
    }

    /**
     * {@inheritdoc}
     */
    public function info($id)
    {
        return $this->metadataService->getAttributeMetadata(
            ProductMetadataServiceInterface::ENTITY_TYPE_PRODUCT,
            $id
        );
    }
}
