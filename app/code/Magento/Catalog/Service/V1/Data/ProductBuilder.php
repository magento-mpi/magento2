<?php

namespace Magento\Catalog\Service\V1\Data;

use Magento\Catalog\Service\V1\ProductMetadataServiceInterface;
use Magento\Framework\Service\Data\Eav\AttributeValueBuilder;

class ProductBuilder extends \Magento\Framework\Service\Data\EAV\AbstractObjectBuilder
{
    /**
     * @var ProductMetadataServiceInterface
     */
    protected $metadataService;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Service\Data\Eav\AttributeValueBuilder $valueBuilde
     * @param ProductMetadataServiceInterface $metadataService
     */
    public function __construct(
        AttributeValueBuilder $valueBuilde,
        ProductMetadataServiceInterface $metadataService
    ) {
        parent::__construct($valueBuilde);
        $this->metadataService = $metadataService;
    }

    /**
     * Template method used to configure the attribute codes for the product attributes
     *
     * @return string[]
     */
    public function getCustomAttributesCodes()
    {
        $attributeCodes = array();
        foreach ($this->metadataService->getCustomProductAttributeMetadata() as $attribute) {
            $attributeCodes[] = $attribute->getAttributeCode();
        }
        return $attributeCodes;
    }
}
