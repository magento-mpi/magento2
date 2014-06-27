<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Fixture\CatalogProductBundle;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class BundleSelections
 * Bundle selections preset
 */
class BundleSelections implements FixtureInterface
{
    /**
     * Fixture factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Current preset
     *
     * @var string
     */
    protected $currentPreset;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $data
     * @param array $params
     * @param bool $persist
     */
    public function __construct(FixtureFactory $fixtureFactory, $data, array $params = [], $persist = false)
    {
        $this->params = $params;

        if ($data['preset']) {
            $this->currentPreset = $data['preset'];
            $this->data = $this->getPreset($this->currentPreset);
            if (!empty($data['products'])) {
                $this->data['products'] = [];
                $this->data['products'] = explode('|', $data['products']);
                foreach ($this->data['products'] as $key => $products) {
                    $this->data['products'][$key] = explode(',', $products);
                }
            }
        }

        if (!empty($this->data['products'])) {
            $productsSelections = $this->data['products'];
            $this->data['products'] = [];
            foreach ($productsSelections as $products) {
                $productSelection = [];
                foreach ($products as $product) {
                    list($fixture, $dataSet) = explode('::', $product);
                    $productSelection[] = $fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
                }
                $this->data['products'][] = $productSelection;
            }
        }

        if ($persist) {
            $this->persist();
        }
    }

    /**
     * Persist bundle selections products
     *
     * @return void
     */
    public function persist()
    {
        if (!empty($this->data['products'])) {
            foreach ($this->data['products'] as $selections) {
                foreach ($selections as $product) {
                    /** @var $product FixtureInterface */
                    $product->persist();
                }
            }

            foreach ($this->data['bundle_options'] as $optionKey => &$bundleOption) {
                foreach ($bundleOption['assigned_products'] as $productKey => &$assignedProducts) {
                    $assignedProducts['search_data']['name'] = $this->data['products'][$optionKey][$productKey]
                        ->getName();
                }
            }
        }
    }

    /**
     * Return prepared data set
     *
     * @param string $key [optional]
     * @return mixed
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings
     *
     * @return string
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Get selection for performing checkout
     *
     * @return array|null
     */
    public function getSelectionForCheckout()
    {
        /** @var \Magento\Catalog\Test\Fixture\CatalogProductSimple $product */
        $product = $this->data['products'][0];
        $selectionsForCheckout = [
            'default' => [
                [
                    'value' => $product->getName(),
                    'type' => 'select',
                    'qty' => 1
                ]
            ],
            'second' => [
                [
                    'value' => $product->getName(),
                    'type' => 'select',
                    'qty' => 1
                ]
            ],
        ];
        if (!isset($selectionsForCheckout[$this->currentPreset])) {
            return null;
        }
        return $selectionsForCheckout[$this->currentPreset];
    }

    /**
     * Preset array
     *
     * @param string $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function getPreset($name)
    {
        $presets = [
            'default_dynamic' => [
                'bundle_options' => [
                    [
                        'title' => 'Drop-down Option',
                        'type' => 'Drop-down',
                        'required' => 'Yes',
                        'assigned_products' => [
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_qty' => 1,
                                ]
                            ],
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_qty' => 1,
                                ]
                            ]
                        ]
                    ],
                ],
                'products' => [
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::default'
                    ]
                ]
            ],
            'default_fixed' => [
                'bundle_options' => [
                    [
                        'title' => 'Drop-down Option',
                        'type' => 'Drop-down',
                        'required' => 'Yes',
                        'assigned_products' => [
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_price_value' => 5.00,
                                    'selection_price_type' => 'Fixed',
                                    'selection_qty' => 1,
                                ]
                            ],
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_price_value' => 5.00,
                                    'selection_price_type' => 'Fixed',
                                    'selection_qty' => 1,
                                ]
                            ]
                        ]
                    ],
                ],
                'products' => [
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::default'
                    ],
                ]
            ],
            'second' => [
                'bundle_options' => [
                    [
                        'title' => 'Drop-down Option',
                        'type' => 'Drop-down',
                        'required' => 'Yes',
                        'assigned_products' => [
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_price_value' => 5.00,
                                    'selection_price_type' => 'Fixed',
                                    'selection_qty' => 1,
                                ]
                            ],
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_price_value' => 10.00,
                                    'selection_price_type' => 'Fixed',
                                    'selection_qty' => 1,
                                ]
                            ]
                        ]
                    ],
                ],
                'products' => [
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::default'
                    ],
                ]
            ],
            'all_types_fixed' => [
                'bundle_options' => [
                    [
                        'title' => 'Drop-down Option',
                        'type' => 'Drop-down',
                        'required' => 'Yes',
                        'assigned_products' => [
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_price_value' => 5.00,
                                    'selection_price_type' => 'Fixed',
                                    'selection_qty' => 1,
                                ]
                            ],
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_price_value' => 5.00,
                                    'selection_price_type' => 'Fixed',
                                    'selection_qty' => 1,
                                ]
                            ]
                        ]
                    ],
                    [
                        'title' => 'Radio Button Option',
                        'type' => 'Radio Buttons',
                        'required' => 'Yes',
                        'assigned_products' => [
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_price_value' => 5.00,
                                    'selection_price_type' => 'Fixed',
                                    'selection_qty' => 1,
                                ]
                            ],
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_price_value' => 5.00,
                                    'selection_price_type' => 'Fixed',
                                    'selection_qty' => 1,
                                ]
                            ]
                        ]
                    ],
                    [
                        'title' => 'Checkbox Option',
                        'type' => 'Checkbox',
                        'required' => 'Yes',
                        'assigned_products' => [
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_price_value' => 5.00,
                                    'selection_price_type' => 'Fixed',
                                    'selection_qty' => 1,
                                ]
                            ],
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_price_value' => 5.00,
                                    'selection_price_type' => 'Fixed',
                                    'selection_qty' => 1,
                                ]
                            ]
                        ]
                    ],
                    [
                        'title' => 'Multiple Select Option',
                        'type' => 'Multiple Select',
                        'required' => 'Yes',
                        'assigned_products' => [
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_price_value' => 5.00,
                                    'selection_price_type' => 'Fixed',
                                    'selection_qty' => 1,
                                ]
                            ],
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_price_value' => 5.00,
                                    'selection_price_type' => 'Fixed',
                                    'selection_qty' => 1,
                                ]
                            ]
                        ]
                    ],
                ],
                'products' => [
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::default'
                    ],
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::default'
                    ],
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::default'
                    ],
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::default'
                    ],
                ]
            ],
            'all_types_dynamic' => [
                'bundle_options' => [
                    [
                        'title' => 'Drop-down Option',
                        'type' => 'Drop-down',
                        'required' => 'Yes',
                        'assigned_products' => [
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_qty' => 1,
                                ]
                            ],
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_qty' => 1,
                                ]
                            ]
                        ]
                    ],
                    [
                        'title' => 'Radio Button Option',
                        'type' => 'Radio Buttons',
                        'required' => 'Yes',
                        'assigned_products' => [
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_qty' => 1,
                                ]
                            ],
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_qty' => 1,
                                ]
                            ]
                        ]
                    ],
                    [
                        'title' => 'Checkbox Option',
                        'type' => 'Checkbox',
                        'required' => 'Yes',
                        'assigned_products' => [
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_qty' => 1,
                                ]
                            ],
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_qty' => 1,
                                ]
                            ]
                        ]
                    ],
                    [
                        'title' => 'Multiple Select Option',
                        'type' => 'Multiple Select',
                        'required' => 'Yes',
                        'assigned_products' => [
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_qty' => 1,
                                ]
                            ],
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_qty' => 1,
                                ]
                            ]
                        ]
                    ],
                ],
                'products' => [
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::default'
                    ],
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::default'
                    ],
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::default'
                    ],
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::default'
                    ],
                ]
            ],
            'with_not_required_options' => [
                'bundle_options' => [
                    [
                        'title' => 'Drop-down Option',
                        'type' => 'Drop-down',
                        'required' => 'No',
                        'assigned_products' => [
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_qty' => 1,
                                    'selection_price_value' => 45,
                                    'selection_price_type' => 'Fixed',
                                ]
                            ],
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_qty' => 1,
                                    'selection_price_value' => 43,
                                    'selection_price_type' => 'Fixed',
                                ]
                            ]
                        ]
                    ],
                    [
                        'title' => 'Radio Button Option',
                        'type' => 'Radio Buttons',
                        'required' => 'No',
                        'assigned_products' => [
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_qty' => 1,
                                    'selection_price_value' => 45,
                                    'selection_price_type' => 'Fixed',
                                ]
                            ],
                            [
                                'search_data' => [
                                    'name' => '%product_name%'
                                ],
                                'data' => [
                                    'selection_qty' => 1,
                                    'selection_price_value' => 43,
                                    'selection_price_type' => 'Fixed',
                                ]
                            ]
                        ]
                    ],
                ],
                'products' => [
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::default'
                    ],
                    [
                        'catalogProductSimple::default',
                        'catalogProductSimple::default'
                    ]
                ]
            ],
        ];
        if (!isset($presets[$name])) {
            throw new \InvalidArgumentException(
                sprintf('Wrong Bundle Selections preset name: %s', $name)
            );
        }
        return $presets[$name];
    }
}
