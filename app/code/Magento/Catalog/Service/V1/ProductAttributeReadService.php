<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

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
     * @param ProductMetadataServiceInterface $metadataService
     */
    public function __construct(ProductMetadataServiceInterface $metadataService)
    {
        $this->metadataService = $metadataService;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        $types = array();
        foreach ($this->metadataService->getProductAttributesMetadata() as $attributeMetadata) {
            $types[] = [
                'value' => $attributeMetadata->getFrontendInput(),
                'label' => $attributeMetadata->getFrontendLabel()
            ];
        }
        return $types;
    }
}
