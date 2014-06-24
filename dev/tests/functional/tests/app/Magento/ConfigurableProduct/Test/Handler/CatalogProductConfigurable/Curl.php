<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Handler\CatalogProductConfigurable;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlTransport;
use Magento\Catalog\Test\Handler\CatalogProductSimple\Curl as ProductCurl;

/**
 * Class Curl
 * Create new configurable product via curl
 */
class Curl extends ProductCurl implements CatalogProductConfigurableInterface
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
                'Yes' => 1,
                'No' => 0
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
     * @param FixtureInterface $fixture
     * @param string|null $prefix [optional]
     * @return array
     */
    protected function prepareData(FixtureInterface $fixture, $prefix = null)
    {
        $data = parent::prepareData($fixture, null);
        $attributeSetId = $data['attribute_set_id'];
        $matrix = $data['configurable_attributes_data']['matrix'];
        $attributesData = $data['configurable_attributes_data']['attributes_data'];
        unset($data['configurable_attributes_data'], $data['attribute_set_id']);

        // Preparing attribute data
        $attributesIds = [];
        $data['configurable_attributes_data'] = [];
        foreach ($attributesData as $attribute) {
            $attributesIds[] = $attribute['id'];
            $data['configurable_attributes_data'][$attribute['id']] = [];
            $dataOption = & $data['configurable_attributes_data'][$attribute['id']];
            $dataOption['code'] = $attribute['title'];
            $dataOption['label'] = $attribute['title'];
            $dataOption['attribute_id'] = $attribute['id'];
            foreach ($attribute['options'] as $option) {
                $dataOption['values'][$option['id']]['pricing_value'] = $option['pricing_value'];
                $dataOption['values'][$option['id']]['is_percent'] = $option['is_percent'];
                $dataOption['values'][$option['id']]['include'] = $option['include'];
                $dataOption['values'][$option['id']]['value_index'] = $option['id'];
            }
        }
        unset($dataOption);

        // Create placeholder
        $placeholder = [
            '%product_name%' => $data['name'],
            '%product_sku%' => $data['sku'],
        ];

        // Add prefix data
        $data = $prefix ? [$prefix => $data] : $data;

        $data['attributes'] = $attributesIds;

        // Preparing matrix data
        foreach (array_keys($matrix) as $key) {
            foreach ($matrix[$key] as &$value) {
                if (is_string($value)) {
                    $value = strtr($value, $placeholder);
                }
            }
        }
        unset($value);

        $data['variations-matrix'] = [];
        $data['associated_product_ids'] = [];
        foreach ($matrix as $key => $variation) {
            $data['associated_product_ids'] = array_merge(
                $data['associated_product_ids'],
                $variation['associated_product_ids']
            );
            $data['variations-matrix'][$key]['name'] = $variation['name'];
            $data['variations-matrix'][$key]['configurable_attribute'] = $variation['configurable_attribute'];
            $data['variations-matrix'][$key]['sku'] = $variation['sku'];
            $data['variations-matrix'][$key]['quantity_and_stock_status']['qty'] = $variation['qty'];
            $data['variations-matrix'][$key]['weight'] = $variation['weight'];
        }
        $data['new-variations-attribute-set-id'] = $attributeSetId;

        return $this->replaceMappingData($data);
    }
}
