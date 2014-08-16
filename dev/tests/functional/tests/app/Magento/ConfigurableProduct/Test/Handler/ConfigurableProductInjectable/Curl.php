<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Handler\ConfigurableProductInjectable;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlTransport;
use Magento\Catalog\Test\Handler\CatalogProductSimple\Curl as ProductCurl;
use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProductInjectable;
use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProductInjectable\ConfigurableAttributesData;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;

/**
 * Class Curl
 * Create new configurable product via curl
 */
class Curl extends ProductCurl implements ConfigurableProductInjectableInterface
{
    /**
     * Constructor
     *
     * @param Config $configuration
     */
    public function __construct(Config $configuration)
    {
        parent::__construct($configuration);

        $this->mappingData += [
            'is_percent' => [
                '%' => 1,
                '$' => 0
            ],
            'include' => [
                'Yes' => 1,
                'No' => 0
            ]
        ];
    }

    /**
     * Prepare POST data for creating product request
     *
     * @param FixtureInterface $product
     * @param string|null $prefix [optional]
     * @return array
     */
    protected function prepareData(FixtureInterface $product, $prefix = null)
    {
        $data = parent::prepareData($product, null);

        /** @var ConfigurableAttributesData $configurableAttributesData */
        $configurableAttributesData = $product->getDataFieldConfig('configurable_attributes_data')['source'];
        $attributeSetId = $data['attribute_set_id'];

        $data['configurable_attributes_data'] = $this->prepareAttributesData($configurableAttributesData);
        $data = $prefix ? [$prefix => $data] : $data;
        $data['variations-matrix'] = $this->prepareVariationsMatrix($configurableAttributesData);
        $data['attributes'] = $this->prepareAttributes($configurableAttributesData);
        $data['new-variations-attribute-set-id'] = $attributeSetId;
        $data['associated_product_ids'] = [];

        return $this->replaceMappingData($data);
    }

    /**
     * Preparing attribute data
     *
     * @param ConfigurableAttributesData $configurableAttributesData
     * @return array
     */
    protected function prepareAttributesData(ConfigurableAttributesData $configurableAttributesData)
    {
        $optionFields = [
            'pricing_value',
            'is_percent',
            'include',
        ];
        $result = [];

        foreach ($configurableAttributesData->getAttributesData() as $attributeKey => $attribute) {
            $attributeId = isset($attribute['attribute_id']) ? $attribute['attribute_id'] : null;
            $dataOptions = [];

            foreach ($attribute['options'] as $optionKey => $option) {
                $optionId = isset($option['id']) ? $option['id'] : null;

                $dataOption['value_index'] = $optionId;
                $dataOption = array_intersect_key($option, array_flip($optionFields));

                $dataOptions[$optionId] = $dataOption;
            }

            $result[$attributeId] = [
                'label' => $attribute['frontend_label'],
                'code' => $attribute['attribute_code'],
                'attribute_id' => $attributeId,
                'values' => $dataOptions
            ];
        }

        return $result;
    }

    /**
     * Preparing matrix data
     *
     * @param ConfigurableAttributesData $configurableAttributesData
     * @return array
     */
    protected function prepareVariationsMatrix(ConfigurableAttributesData $configurableAttributesData)
    {
        $attributesData = $configurableAttributesData->getAttributesData();
        $result = [];

        foreach ($configurableAttributesData->getVariationsMatrix() as $variationKey => $variation) {
            $compositeKeys = explode(' ', $variationKey);
            $keyIds = [];
            $configurableAttribute = [];

            foreach ($compositeKeys as $compositeKey) {
                list($attributeKey, $optionKey) = explode(':', $compositeKey);
                $attribute = $attributesData[$attributeKey];

                $keyIds[] = $attribute['options'][$optionKey]['id'];
                $configurableAttribute[] = sprintf(
                    '"%s":"%s"',
                    $attribute['attribute_code'],
                    $attribute['options'][$optionKey]['id']
                );
            }

            $keyIds = implode('-', $keyIds);
            $variation['configurable_attribute'] = '{' . implode(',', $configurableAttribute) . '}';
            $result[$keyIds] = $variation;
        }

        return $result;
    }

    /**
     * Prepare attributes
     *
     * @param ConfigurableAttributesData $configurableAttributesData
     * @return array
     */
    protected function prepareAttributes(ConfigurableAttributesData $configurableAttributesData)
    {
        $ids = [];

        foreach ($configurableAttributesData->getAttributes() as $attribute) {
            /** @var CatalogProductAttribute $attribute */
            $ids[] = $attribute->getAttributeId();
        }
        return $ids;
    }
}
