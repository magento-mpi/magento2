<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Fixture\ConfigurableProductInjectable;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\InjectableFixture;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class ConfigurableAttributesData
 * Source configurable attributes data of the configurable products
 */
class ConfigurableAttributesData implements FixtureInterface
{
    /**
     * Fixture factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Data set configuration settings
     *
     * @var array
     */
    protected $params;

    /**
     * Prepared dataSet data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Prepared attributes data
     *
     * @var array
     */
    protected $attributesData = [];

    /**
     * Prepared variation matrix
     *
     * @var array
     */
    protected $variationsMatrix = [];

    /**
     * Prepared attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Preapred products
     *
     * @var array
     */
    protected $products = [];

    /**
     * Presets data
     *
     * @var array
     */
    protected $presets = [
        'default' => [
            'attributes_data' => [
                'attribute_0' => [
                    'options' => [
                        'option_0' => [
                            'pricing_value' => 12.00,
                            'include' => 'Yes',
                            'is_percent' => '$'
                        ],
                        'option_1' => [
                            'pricing_value' => 20.00,
                            'include' => 'Yes',
                            'is_percent' => '$'
                        ],
                        'option_2' => [
                            'pricing_value' => 18.00,
                            'include' => 'Yes',
                            'is_percent' => '$'
                        ],
                    ]
                ],
                'attribute_1' => [
                    'options' => [
                        'option_0' => [
                            'pricing_value' => 42.00,
                            'include' => 'Yes',
                            'is_percent' => '$'
                        ],
                        'option_1' => [
                            'pricing_value' => 40.00,
                            'include' => 'Yes',
                            'is_percent' => '$'
                        ],
                        'option_2' => [
                            'pricing_value' => 48.00,
                            'include' => 'Yes',
                            'is_percent' => '$'
                        ],
                    ]
                ]
            ],
            'products' => [

            ],
            'attributes' => [
                'attribute_0' => 'catalogProductAttribute::attribute_type_dropdown',
                'attribute_1' => 'catalogProductAttribute::attribute_type_dropdown'
            ],
            'matrix' => [
                'attribute_0:option_0 attribute_1:option_0' => [
                    'quantity_and_stock_status' => [
                        'qty' => 10
                    ],
                    'weight' => 1
                ],
                'attribute_0:option_0 attribute_1:option_1' => [
                    'quantity_and_stock_status' => [
                        'qty' => 10
                    ],
                    'weight' => 1
                ],
                'attribute_0:option_0 attribute_1:option_2' => [
                    'quantity_and_stock_status' => [
                        'qty' => 10
                    ],
                    'weight' => 1
                ],
                'attribute_0:option_1 attribute_1:option_0' => [
                    'quantity_and_stock_status' => [
                        'qty' => 10
                    ],
                    'weight' => 1
                ],
                'attribute_0:option_1 attribute_1:option_1' => [
                    'quantity_and_stock_status' => [
                        'qty' => 10
                    ],
                    'weight' => 1
                ],
                'attribute_0:option_1 attribute_1:option_2' => [
                    'quantity_and_stock_status' => [
                        'qty' => 10
                    ],
                    'weight' => 1
                ],
                'attribute_0:option_2 attribute_1:option_0' => [
                    'quantity_and_stock_status' => [
                        'qty' => 10
                    ],
                    'weight' => 1
                ],
                'attribute_0:option_2 attribute_1:option_1' => [
                    'quantity_and_stock_status' => [
                        'qty' => 10
                    ],
                    'weight' => 1
                ],
                'attribute_0:option_2 attribute_1:option_2' => [
                    'quantity_and_stock_status' => [
                        'qty' => 10
                    ],
                    'weight' => 1
                ],
            ]
        ],
        'one_variation' => [
            'attributes_data' => [
                [
                    'id' => '%id%',
                    'title' => '%title%',
                    'label' => 'Test variation1 label',
                    'options' => [
                        [
                            'id' => '%id%',
                            'name' => '%name%',
                            'pricing_value' => 12.00,
                            'include' => 'Yes',
                            'is_percent' => 'No'
                        ]
                    ]
                ]
            ],
            'products' => [

            ],
            'attributes' => [
                'catalogProductAttribute::attribute_type_dropdown'
            ],
            'matrix' => [
                '%attribute_0-option_0%' => [
                    'configurable_attribute' => [
                        '%attribute_0_code%' => '%attribute_0-option_0%',
                    ],
                    'associated_product_ids' => [],
                    'name' => 'In configurable %isolation% %attribute_0-option_0_name%',
                    'sku' => 'sku_configurable_%isolation%_%attribute_0-option_0_id%',
                    'qty' => 10,
                    'weight' => 1,
                    'options_names' => []
                ]
            ]
        ],
        'two_options' => [
            'attributes_data' => [
                'attribute_0' => [
                    'options' => [
                        'option_0' => [
                            'label' => 'option_1_%isolation%',
                            'pricing_value' => 1,
                            'is_percent' => '%',
                            'include' => 'Yes'
                        ],
                        'option_1' => [
                            'label' => 'option_2_%isolation%',
                            'pricing_value' => 2,
                            'is_percent' => '%',
                            'include' => 'Yes',
                        ]
                    ]
                ]
            ],
            'attributes' => [
                'attribute_0' => 'catalogProductAttribute::attribute_type_dropdown_two_options',
            ],
            'products' => [],
            'matrix' => [
                'attribute_0:option_0' => [
                    'display' => 'Yes',
                    'quantity_and_stock_status' => [
                        'qty' => 10
                    ],
                    'weight' => 1
                ],
                'attribute_0:option_1' => [
                    'display' => 'Yes',
                    'quantity_and_stock_status' => [
                        'qty' => 20
                    ],
                    'weight' => 2
                ]
            ]
        ],
        'two_new_options' => [
            'attributes_data' => [
                'attribute_0' => [
                    'frontend_label' => 'two_new_options_title_%isolation%',
                    'frontend_input' => 'Dropdown',
                    'label' => 'two_new_options_title_%isolation%',
                    'is_required' => 'No',
                    'options' => [
                        'option_0' => [
                            'label' => 'option_1_%isolation%',
                            'pricing_value' => 1,
                            'is_percent' => '$',
                            'include' => 'Yes'
                        ],
                        'option_1' => [
                            'label' => 'option_2_%isolation%',
                            'pricing_value' => 2,
                            'is_percent' => '$',
                            'include' => 'Yes',
                        ]
                    ]
                ]
            ],
            'attributes' => [],
            'products' => [],
            'matrix' => [
                'attribute_0:option_0' => [
                    'display' => 'Yes',
                    'quantity_and_stock_status' => [
                        'qty' => 10
                    ],
                    'weight' => 1
                ],
                'attribute_0:option_1' => [
                    'display' => 'Yes',
                    'quantity_and_stock_status' => [
                        'qty' => 20
                    ],
                    'weight' => 2
                ]
            ]
        ],
        'two_options_with_assigned_product' => [
            'attributes_data' => [
                'attribute_0' => [
                    'options' => [
                        'option_0' => [
                            'label' => 'option_1_%isolation%',
                            'pricing_value' => 1,
                            'is_percent' => '%',
                            'include' => 'Yes'
                        ],
                        'option_1' => [
                            'label' => 'option_2_%isolation%',
                            'pricing_value' => 2,
                            'is_percent' => '%',
                            'include' => 'Yes',
                        ]
                    ]
                ]
            ],
            'attributes' => [
                'attribute_0' => 'catalogProductAttribute::attribute_type_dropdown_two_options',
            ],
            'products' => [
                'attribute_0:option_0' => 'catalogProductSimple::default',
                'attribute_0:option_1' => 'catalogProductSimple::default'
            ],
            'matrix' => [
                'attribute_0:option_0' => [
                    'display' => 'Yes',
                    'quantity_and_stock_status' => [
                        'qty' => 10
                    ],
                    'weight' => 1
                ],
                'attribute_0:option_1' => [
                    'display' => 'Yes',
                    'quantity_and_stock_status' => [
                        'qty' => 20
                    ],
                    'weight' => 2
                ]
            ]
        ]
    ];


    /**
     * Source constructor
     *
     * @param FixtureFactory $fixtureFactory
     * @param array $data
     * @param array $params [optional]
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function __construct(FixtureFactory $fixtureFactory, array $data, array $params = [])
    {
        $this->fixtureFactory = $fixtureFactory;
        $this->params = $params;

        $preset = isset($data['preset']) ? $this->getPreset($data['preset']) : [];
        unset($data['preset']);

        $this->prepareAttributes($preset);
        $this->prepareAttributesData($preset);
        $this->prepareProducts($preset);
        $this->prepareVariationsMatrix($preset);
        $this->prepareData();
    }

    /**
     * Persist configurable attribute data
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Prepare attributes
     *
     * @param array $data
     * @return void
     */
    protected function prepareAttributes(array $data)
    {
        if (empty($data['attributes'])) {
            return;
        }

        foreach ($data['attributes'] as $key => $attribute) {
            if (is_string($attribute)) {
                list($fixture, $dataSet) = explode('::', $attribute);
                /** @var InjectableFixture $attribute */
                $attribute = $this->fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
            }
            if (!$attribute->hasData('attribute_id')) {
                $attribute->persist();
            }
            $this->attributes[$key] = $attribute;
        }
    }

    /**
     * Prepare attributes data
     *
     * @param array $data
     * @return void
     */
    protected function prepareAttributesData(array $data)
    {
        foreach ($this->attributes as $attributeKey => $attribute) {
            $attributeData = $attribute->getData();
            $options = [];

            foreach ($attributeData['options'] as $key => $option) {
                $options['option_' . $key] = $option;
            }
            $attributeData['options'] = $options;

            $this->attributesData[$attributeKey] = $attributeData;
        }

        $this->attributesData = array_replace_recursive(
            isset($data['attributes_data']) ? $data['attributes_data'] : [],
            $this->attributesData
        );
    }

    /**
     * Prepare products
     *
     * @param array $data
     * @return void
     */
    protected function prepareProducts(array $data)
    {
        if (empty($data['products'])) {
            return;
        }

        $attributeSetData = [];
        if (!empty($this->attributes)) {
            $attributeSetData['attribute_set_id'] = ['attribute_set' => $this->createAttributeSet()];
        }

        foreach ($data['products'] as $key => $product) {
            if (is_string($product)) {
                list($fixture, $dataSet) = explode('::', $product);
                $attributeData = ['attributes' => $this->getProductAttributeData($key)];
                $product = $this->fixtureFactory->createByCode(
                    $fixture,
                    ['dataSet' => $dataSet, 'data' => array_merge($attributeSetData, $attributeData)]
                );
            }
            if (!$product->hasData('id')) {
                $product->persist();
            }

            $this->products[$key] = $product;
        }
    }

    /**
     * Create attribute set
     *
     * @return FixtureInterface
     */
    protected function createAttributeSet()
    {
        $attributeSet = $this->fixtureFactory->createByCode(
            'catalogAttributeSet',
            [
                'dataSet' => 'custom_attribute_set',
                'data' => [
                    'assigned_attributes' => [
                        'presets' => array_values($this->attributes)
                    ]
                ]
            ]
        );
        $attributeSet->persist();
        return $attributeSet;
    }

    /**
     * Get prepared attribute data for persist product
     *
     * @param string $key
     * @return array
     */
    protected function getProductAttributeData($key)
    {
        $compositeKeys = explode(' ', $key);
        $data = [];

        foreach ($compositeKeys as $compositeKey) {
            $attributeId = $this->getAttributeOptionId($compositeKey);
            if ($attributeId) {
                $compositeKey = explode(':', $compositeKey);
                $attributeKey = $compositeKey[0];
                $data[$this->attributesData[$attributeKey]['attribute_code']] = $attributeId;
            }
        }

        return $data;
    }

    /**
     * Get id of attribute option by composite key
     *
     * @param string $compositeKey
     * @return int|null
     */
    protected function getAttributeOptionId($compositeKey)
    {
        list($attributeKey, $optionKey) = explode(':', $compositeKey);
        return isset($this->attributesData[$attributeKey]['options'][$optionKey]['id'])
            ? $this->attributesData[$attributeKey]['options'][$optionKey]['id']
            : null;
    }

    /**
     * Prepare data for matrix
     *
     * @param array $data
     * @return void
     */
    protected function prepareVariationsMatrix(array $data)
    {
        $variationsMatrix = [];

        // generate matrix
        foreach ($this->attributesData as $attributeKey => $attribute) {
            $variationsMatrix = $this->addVariationMatrix($variationsMatrix, $attribute, $attributeKey);
        }
        $this->variationsMatrix = array_replace_recursive($variationsMatrix, $data['matrix']);

        // assigned products
        foreach ($this->variationsMatrix as $key => $row) {
            if (isset($this->products[$key])) {
                /** @var CatalogProductSimple $product */
                $product = $this->products[$key];
                $quantityAndStockStatus = $product->getQuantityAndStockStatus();

                $this->variationsMatrix[$key]['configurable_attribute'] = $product->getId();
                $this->variationsMatrix[$key]['name'] = $product->getName();
                $this->variationsMatrix[$key]['sku'] = $product->getSku();
                $this->variationsMatrix[$key]['quantity_and_stock_status']['qty'] = $quantityAndStockStatus['qty'];
                $this->variationsMatrix[$key]['weight'] = $product->getWeight();
            }
        }
    }

    /**
     * Add matrix variation
     *
     * @param array $variationsMatrix
     * @param array $attribute
     * @param string $attributeKey
     * @return array
     */
    protected function addVariationMatrix(array $variationsMatrix, array $attribute, $attributeKey)
    {
        $result = [];

        /* If empty matrix add one empty row */
        if (empty($variationsMatrix)) {
            $variationsMatrix = [
                [
                    'name' => 'In configurable product %isolation%',
                    'sku' => 'in_configurable_product_%isolation%',
                ]
            ];
        }

        foreach ($variationsMatrix as $rowKey => $row) {
            foreach ($attribute['options'] as $optionKey => $option) {
                $compositeKey = "{$attributeKey}:{$optionKey}";
                $optionId = $this->getAttributeOptionId($compositeKey);

                $label = isset($option['label'])
                    ? str_replace('%isolation%', '', $option['label'])
                    : '';
                $row['name'] .= '-' . $label;
                $row['sku'] .= '_' . $optionId;

                $newRowKey = $rowKey ? "{$rowKey} {$compositeKey}" : $compositeKey;
                $result[$newRowKey] = $row;
            }
        }

        return $result;
    }

    /**
     * Prepare data from source
     *
     * @return void
     */
    protected function prepareData()
    {
        $attributeFields = [
            'frontend_label',
            'label',
            'frontend_input',
            'attribute_code',
            'attribute_id',
            'is_required',
            'options',
        ];
        $optionFields = [
            'label',
            'pricing_value',
            'is_percent',
            'include',
        ];
        $variationMatrixFields = [
            'configurable_attribute',
            'display',
            'name',
            'sku',
            'price',
            'quantity_and_stock_status',
            'weight',
        ];

        $this->data = [
            'matrix' => [],
            'attributes_data' => []
        ];

        foreach ($this->attributesData as $attributeKey => $attribute) {
            foreach ($attribute['options'] as $optionKey => $option) {
                $option['label'] = isset($option['view']) ? $option['view'] : $option['label'];
                $attribute['options'][$optionKey] = array_intersect_key($option, array_flip($optionFields));
            }
            $attribute['label'] = isset($attribute['label']) ? $attribute['label'] : $attribute['frontend_label'];
            $attribute = array_intersect_key($attribute, array_flip($attributeFields));

            $this->data['attributes_data'][$attributeKey] = $attribute;
        }
        foreach ($this->variationsMatrix as $key => $variationMatrix) {
            $this->data['matrix'][$key] = array_intersect_key($variationMatrix, array_flip($variationMatrixFields));
        }
    }

    /**
     * Return data set configuration settings
     *
     * @return array
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Return prepared data set
     *
     * @param string|null $key
     * @return mixed
     */
    public function getData($key = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $this->data;
    }

    /**
     * Get prepared attributes data
     *
     * @return array
     */
    public function getAttributesData()
    {
        return $this->attributesData;
    }

    /**
     * Get prepared variations matrix
     *
     * @return array
     */
    public function getVariationsMatrix()
    {
        return $this->variationsMatrix;
    }

    /**
     * Get preapared attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get prepared products
     *
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Preset array
     *
     * @param string $name
     * @return mixed|null
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getPreset($name)
    {
        if (!isset($this->presets[$name])) {
            return null;
        }
        return $this->presets[$name];
    }
}
