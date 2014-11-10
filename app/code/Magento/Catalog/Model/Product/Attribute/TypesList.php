<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\Attribute;

use Magento\Catalog\Model\Product\Attribute\InputtypeFactory;
use Magento\Catalog\Model\Product\Attribute\MetadataServiceInterface;
use Magento\Catalog\Model\Product\Attribute\TypeBuilder;

class TypesList implements \Magento\Catalog\Api\ProductAttributeTypesListInterface
{
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\InputtypeFactory
     */
    private $inputTypeFactory;

    /**
     * @var \Magento\Catalog\Api\Data\ProductAttributeTypeDataBuilder
     */
    private $attributeTypeBuilder;

    /**
     * @param Source\InputtypeFactory $inputTypeFactory
     * @param \Magento\Catalog\Api\Data\ProductAttributeTypeDataBuilder $attributeTypeBuilder
     */
    public function __construct(
        \Magento\Catalog\Model\Product\Attribute\Source\InputtypeFactory $inputTypeFactory,
        \Magento\Catalog\Api\Data\ProductAttributeTypeDataBuilder $attributeTypeBuilder
    ) {
        $this->inputTypeFactory = $inputTypeFactory;
        $this->attributeTypeBuilder = $attributeTypeBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        $types = [];
        $inputType = $this->inputTypeFactory->create();

        foreach ($inputType->toOptionArray() as $option) {
            $types[] = $this->attributeTypeBuilder->populateWithArray($option)->create();
        }
        return $types;
    }
}
