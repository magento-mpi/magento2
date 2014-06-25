<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute;

use Magento\Catalog\Model\Product\Attribute\Source\InputtypeFactory;
use Magento\Catalog\Service\V1\Data\Eav\Product\Attribute\TypeBuilder;
use Magento\Catalog\Service\V1\MetadataServiceInterface;
use Magento\Catalog\Service\V1\Product\MetadataServiceInterface as ProductMetadataServiceInterface;

/**
 * Class ReadService
 *
 * @package Magento\Catalog\Service\V1\Product\Attribute
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ReadService implements ReadServiceInterface
{
    /**
     * @var MetadataServiceInterface
     */
    private $metadataService;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\InputtypeFactory
     */
    private $inputTypeFactory;

    /**
     * @var TypeBuilder
     */
    private $attributeTypeBuilder;

    /**
     * @param MetadataServiceInterface $metadataService
     * @param InputtypeFactory $inputTypeFactory
     * @param TypeBuilder $attributeTypeBuilder
     */
    public function __construct(
        MetadataServiceInterface $metadataService,
        InputtypeFactory $inputTypeFactory,
        TypeBuilder $attributeTypeBuilder
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
            ProductMetadataServiceInterface::ENTITY_TYPE,
            $id
        );
    }

    /**
     * {@inheritdoc}
     */
    public function search(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria)
    {
        return $this->metadataService->getAllAttributeMetadata(
            ProductMetadataServiceInterface::ENTITY_TYPE,
            $searchCriteria
        );
    }
}
