<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model;

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
        $productBuilder = $this->_populateBuilderWithAttributes($productModel);
        return $productBuilder->create();
    }

    /**
     * Loads the values from a product model
     *
     * @param \Magento\Catalog\Model\Product $productModel
     * @return \Magento\Catalog\Service\V1\Data\ProductBuilder
     */
    protected function _populateBuilderWithAttributes(\Magento\Catalog\Model\Product $productModel)
    {
        $attributes = array();
        foreach ($productModel->getAttributes() as $attribute) {
            $attrCode = $attribute->getAttributeCode();
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

        return $this->productBuilder->populateWithArray($attributes);
    }
}