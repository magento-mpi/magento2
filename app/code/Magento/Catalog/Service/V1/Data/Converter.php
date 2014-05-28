<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data;

use Magento\Catalog\Service\V1\Data\ProductBuilder;
use Magento\Catalog\Service\V1\Data\Product as ProductDataObject;

/**
 * Product Model converter.
 *
 * Converts a Product Model to a Data Object or vice versa.
 */
class Converter
{
    /**
     * @var ProductBuilder
     */
    protected $productBuilder;

    /**
     * @param ProductBuilder $customerBuilder
     */
    public function __construct(ProductBuilder $productBuilder)
    {
        $this->productBuilder = $productBuilder;
    }

    /**
     * Convert a product model to a product data entity
     *
     * @param \Magento\Catalog\Model\Product $productModel
     * @return \Magento\Catalog\Service\V1\Data\Product
     */
    public function createProductDataFromModel(\Magento\Catalog\Model\Product $productModel)
    {
        $this->_populateBuilderWithAttributes($productModel);
        return $this->productBuilder->create();
    }

    /**
     * Loads the values from a product model
     *
     * @param \Magento\Catalog\Model\Product $productModel
     * @return void
     */
    protected function _populateBuilderWithAttributes(\Magento\Catalog\Model\Product $productModel)
    {
        $attributes = array();
        foreach ($this->productBuilder->getCustomAttributesCodes() as $attrCode) {
            $value = $productModel->getDataUsingMethod($attrCode);
            $value = $value ? $value : $productModel->getData($attrCode);
            if (null !== $value) {
                if ($attrCode == 'entity_id') {
                    $attributes[ProductDataObject::ID] = $value;
                } else {
                    $attributes[$attrCode] = $value;
                }
            }
        }

        $this->productBuilder->populateWithArray($attributes);
        return;
    }
}