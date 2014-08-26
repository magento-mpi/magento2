<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data;

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
     * @param ProductBuilder $productBuilder
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
        return $this->createProductBuilderFromModel($productModel)->create();
    }

    /**
     * Initialize product builder with product model data
     *
     * @param \Magento\Catalog\Model\Product $productModel
     * @return \Magento\Catalog\Service\V1\Data\ProductBuilder
     */
    public function createProductBuilderFromModel(\Magento\Catalog\Model\Product $productModel)
    {
        $this->populateBuilderWithAttributes($productModel);
        return $this->productBuilder;
    }

    /**
     * Loads the values from a product model
     *
     * @param \Magento\Catalog\Model\Product $productModel
     * @return void
     */
    protected function populateBuilderWithAttributes(\Magento\Catalog\Model\Product $productModel)
    {
        $attributes = array();
        foreach ($productModel->getAttributes() as $attribute) {
            $attrCode = $attribute->getAttributeCode();
            $value = $productModel->getDataUsingMethod($attrCode) ?: $productModel->getData($attrCode);
            if (null !== $value) {
                if ($attrCode != 'entity_id') {
                    $attributes[$attrCode] = $value;
                }
            }
        }
        $attributes[ProductDataObject::STORE_ID] = $productModel->getStoreId();
        $this->productBuilder->populateWithArray($attributes);
        return;
    }
}
