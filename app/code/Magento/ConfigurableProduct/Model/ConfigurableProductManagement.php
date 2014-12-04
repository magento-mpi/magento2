<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Model;

class ConfigurableProductManagement implements \Magento\ConfigurableProduct\Api\ConfigurableProductManagementInterface
{
    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\VariationMatrix
     */
    private $variationMatrix;

    /**
     * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var \Magento\Framework\Api\AttributeDataBuilder
     */
    private $customAttributeBuilder;

    /**
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository
     * @param Product\Type\VariationMatrix $variationMatrix
     * @param \Magento\Framework\Api\AttributeDataBuilder $customAttributeBuilder
     */
    public function __construct(
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        \Magento\ConfigurableProduct\Model\Product\Type\VariationMatrix $variationMatrix,
        \Magento\Framework\Api\AttributeDataBuilder $customAttributeBuilder
    ) {
        $this->variationMatrix = $variationMatrix;
        $this->attributeRepository = $attributeRepository;
        $this->customAttributeBuilder = $customAttributeBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function generateVariation(\Magento\Catalog\Api\Data\ProductInterface $product, $options)
    {
        $attributes = $this->getAttributesForMatrix($options);
        $variations = $this->variationMatrix->getVariations($attributes);
        $products = $this->populateProductVariation($product, $variations, $attributes);
        return $products;
    }

    /**
     * Prepare attribute info for variation matrix generation
     *
     * @param \Magento\ConfigurableProduct\Api\Data\OptionInterface[] $options
     * @return array
     */
    private function getAttributesForMatrix($options)
    {
        $attributes = [];
        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute $option */
        foreach ($options as $option) {
            $configurable = $this->objectToArray($option);

            if (isset($configurable['values']) && is_array($configurable['values'])) {
                $newValues = [];
                foreach ($configurable['values'] as $value) {
                    $newValues[] = [
                        'value_index' => isset($value['index']) ? $value['index'] : null,
                        'is_percent' => isset($value['is_percent']) ? $value['is_percent'] : null,
                        'pricing_value' => isset($value['price']) ? $value['price'] : null,
                    ];
                }
                $configurable['values'] = $newValues;
            }
            /** @var \Magento\Catalog\Model\Resource\Eav\Attribute $attribute */
            $attribute = $this->attributeRepository->get($option->getAttributeId());
            $attributeOptions = !is_null($attribute->getOptions()) ? $attribute->getOptions() : [];

            foreach ($attributeOptions as $attributeOption) {
                $configurable['options'][] = $this->objectToArray($attributeOption);
            }
            $configurable['attribute_code'] = $attribute->getAttributeCode();
            $attributes[$option->getAttributeId()] = $configurable;
        }
        return $attributes;
    }

    /**
     * Populate product with variation of attributes
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param array $variations
     * @param array $attributes
     * @return array
     */
    private function populateProductVariation(
        \Magento\Catalog\Api\Data\ProductInterface $product,
        $variations,
        $attributes
    ) {
        $products = [];
        foreach ($variations as $attributeId => $variation) {
            $price = $product->getPrice();
            $suffix = '';
            foreach ($variation as $attributeId => $valueInfo) {
                $suffix .= '-' . $valueInfo['value'];
                $customAttribute = $this->customAttributeBuilder
                    ->setAttributeCode($attributes[$attributeId]['attribute_code'])
                    ->setValue($valueInfo['value'])
                    ->create();
                $product->setData(
                    'custom_attributes',
                    array_merge($product->getCustomAttributes(), [$customAttribute])
                );
                $priceInfo = $valueInfo['price'];
                $price += (!empty($priceInfo['is_percent']) ? $product->getPrice() / 100.0 : 1.0)
                    * $priceInfo['pricing_value'];
            }
            $product->setPrice($price);
            $product->setName($product->getName() . $suffix);
            $product->setSku($product->getSku() . $suffix);
            $product->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE);
            $products[] = $product;
        }
        return $products;
    }

    /**
     * Return Data Object data in array format.
     *
     * @param \Magento\Framework\Object $object
     * @return array
     */
    private function objectToArray(\Magento\Framework\Object $object)
    {
        $data = $object->getData();
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                $data[$key] = $this->objectToArray($value);
            } elseif (is_array($value)) {
                foreach ($value as $nestedKey => $nestedValue) {
                    if (is_object($nestedValue)) {
                        $value[$nestedKey] = $this->objectToArray($nestedValue);
                    }
                }
                $data[$key] = $value;
            }
        }
        return $data;
    }
}
