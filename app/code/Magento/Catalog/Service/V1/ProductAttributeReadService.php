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

    private $inputTypeFactory;

    /**
     * @param ProductMetadataServiceInterface $metadataService
     * @param InputtypeFactory $inputTypeFactory
     */
    public function __construct(
        ProductMetadataServiceInterface $metadataService,
        InputtypeFactory $inputTypeFactory
    )
    {
        $this->metadataService = $metadataService;
        $this->inputTypeFactory = $inputTypeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function types()
    {
        $inputType = $this->inputTypeFactory->create();
        return $inputType->toOptionArray();
    }

    /**
     * {@inheritdoc}
     */
    public function info($identifier)
    {
        return $this->metadataService->getAttributeMetadata(
            ProductMetadataServiceInterface::ENTITY_TYPE_PRODUCT,
            $identifier
        );
    }
}
