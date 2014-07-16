<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1;

use Magento\Catalog\Service\V1\Data\Product;
use Magento\Catalog\Service\V1\Data\ProductBuilder;
use Magento\Catalog\Service\V1\Product\Attribute;
use Magento\ConfigurableProduct\Model\Product\Type\VariationMatrix;

class ReadService implements ReadServiceInterface
{
    /**
     * @var VariationMatrix
     */
    private $variationMatrix;

    /**
     * @var ProductBuilder
     */
    private $productBuilder;

    /**
     * @var Attribute\ReadServiceInterface
     */
    private $attributeReadService;

    /**
     * @param Attribute\ReadServiceInterface $attributeReadService
     * @param ProductBuilder $productBuilder
     * @param VariationMatrix $variationMatrix
     */
    public function __construct(
        Attribute\ReadServiceInterface $attributeReadService,
        ProductBuilder $productBuilder,
        VariationMatrix $variationMatrix
    ) {
        $this->variationMatrix = $variationMatrix;
        $this->productBuilder = $productBuilder;
        $this->attributeReadService = $attributeReadService;
    }

    /**
     * {@inheritdoc}
     */
    public function generateVariation(Product $product, $configurableAttributes)
    {
        $attributes = $this->getAttributesForMatrix($configurableAttributes);
        $variations = $this->variationMatrix->getVariations($attributes);
        $products = $this->populateProductVariation($product, $variations, $attributes);
        return $products;
    }

    /**
     * Prepare attribute info for variation matrix generation
     *
     * @param \Magento\ConfigurableProduct\Service\V1\Data\ConfigurableAttribute[] $configurableAttributes
     * @return array
     */
    private function getAttributesForMatrix($configurableAttributes)
    {
        $attributes = [];
        foreach ($configurableAttributes as $configurableAttribute) {
            $configurable = $configurableAttribute->__toArray();
            $attribute = $this->attributeReadService->info($configurableAttribute->getAttributeId());
            $configurable['options'] = $attribute->__toArray()['options'];
            $configurable['attribute_code'] = $attribute->getAttributeCode();
            $attributes[$configurableAttribute->getAttributeId()] = $configurable;
        }
        return $attributes;
    }

    /**
     * Populate product with variation of attributes
     *
     * @param Product $product
     * @param $variations
     * @param $attributes
     * @return array
     */
    private function populateProductVariation(Product $product, $variations, $attributes)
    {
        $products = [];
        foreach ($variations as $variation) {
            $price = $product->getPrice();
            $this->productBuilder->populate($product);
            foreach ($variation as $attributeId => $valueInfo) {
                $this->productBuilder->setCustomAttribute(
                    $attributes[$attributeId]['attribute_code'],
                    $valueInfo['value']
                );
                $priceInfo = $valueInfo['price'];
                $price += (!empty($priceInfo['is_percent']) ? $product->getPrice() / 100.0 : 1.0)
                    * $priceInfo['pricing_value'];
            }
            $this->productBuilder->setPrice($price);
            $products[] = $this->productBuilder->create();
        }
        return $products;
    }
}