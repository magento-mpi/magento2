<?php

namespace Magento\Catalog\Service\V1\Data;

use Magento\Catalog\Service\V1\ProductMetadataServiceInterface;

class ProductBuilder extends \Magento\Service\Data\EAV\AbstractObjectBuilder
{
    /**
     * @var ProductMetadataServiceInterface
     */
    protected $metadataService;

    /**
     * Initialize dependencies.
     *
     * @param ProductMetadataServiceInterface $metadataService
     */
    public function __construct(ProductMetadataServiceInterface $metadataService)
    {
        parent::__construct();
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

    /**
     * {@inheritdoc}
     */
    protected function _setDataValues(array $data)
    {
//        if (array_key_exists(Address::KEY_REGION, $data)) {
//            if (!is_array($data[Address::KEY_REGION])) {
//                // Region data has been submitted as individual keys of Address object. Let's extract it.
//                $regionData = array();
//                foreach (array(Region::KEY_REGION, Region::KEY_REGION_CODE, Region::KEY_REGION_ID) as $attrCode) {
//                    if (isset($data[$attrCode])) {
//                        $regionData[$attrCode] = $data[$attrCode];
//                    }
//                }
//            } else {
//                $regionData = $data[Address::KEY_REGION];
//            }
//            $data[Address::KEY_REGION] = $this->_regionBuilder->populateWithArray($regionData)->create();
//        }
        return parent::_setDataValues($data);
    }
}
