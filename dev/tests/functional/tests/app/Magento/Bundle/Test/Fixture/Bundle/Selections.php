<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Fixture\Bundle;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class Selections
 * Bundle selections preset
 */
class Selections implements FixtureInterface
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
    public function __construct(
        FixtureFactory $fixtureFactory,
        $data,
        array $params = [],
        $persist = false
    ) {
        $this->fixtureFactory = $fixtureFactory;

        $this->data = $data;

        if (isset($this->data['products'])) {
            $products = explode(',', $this->data['products']);
            $this->data['products'] = [];
            foreach ($products as $key => $product) {
                list($fixture, $dataSet) = explode('::', $product);
                $this->data['products'][$key] = $this->fixtureFactory
                    ->createByCode($fixture, ['dataSet' => $dataSet]);
            }
        }
        $this->currentPreset = $this->data['preset'];
        $this->data['preset'] = $this->getPreset($this->data['preset']);

        $this->params = $params;
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
        if (isset($this->data['products'])) {
            foreach ($this->data['products'] as $product) {
                /** @var FixtureInterface $product */
                $product->persist();
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
            'defaultBundleDynamic' => [
                'bundle_option_0' => [
                    'title' => 'Drop-down Option',
                    'type' => 'Drop-down',
                    'required' => 'Yes',
                    'assigned_products' => [
                        [
                            'search_data' => [
                                'name' => '%item1_simple1::getProductName%',
                            ],
                            'data' => [
                                'selection_qty' => 1,
                            ]
                        ],
                        [
                            'search_data' => [
                                'name' => '%item1_virtual2::getProductName%',
                            ],
                            'data' => [
                                'selection_qty' => 1,
                            ]
                        ]
                    ]
                ]
            ],
            'defaultBundleFixed' => [
                'bundle_option_0' => [
                    'title' => 'Drop-down Option',
                    'type' => 'Drop-down',
                    'required' => 'Yes',
                    'assigned_products' => [
                        [
                            'search_data' => [
                                'name' => '%item1_simple1::getProductName%',
                            ],
                            'data' => [
                                'selection_qty' => 1,
                                'selection_price_value' => 10,
                                'selection_price_type' => 'Fixed',
                            ]
                        ],
                        [
                            'search_data' => [
                                'name' => '%item1_virtual2::getProductName%',
                            ],
                            'data' => [
                                'selection_qty' => 1,
                                'selection_price_value' => 5,
                                'selection_price_type' => 'Fixed',
                            ]
                        ]
                    ]
                ]
            ],
            'all_types' => [
                'bundle_option_0' => [
                    'title' => 'Drop-down Option',
                    'type' => 'Drop-down',
                    'required' => 'Yes',
                    'assigned_products' => [
                        [
                            'search_data' => [
                                'name' => '%item1_simple1::getProductName%',
                            ],
                            'data' => [
                                'selection_qty' => 1,
                                'selection_price_value' => 10,
                                'selection_price_type' => 'Fixed',
                            ]
                        ],
                        [
                            'search_data' => [
                                'name' => '%item1_virtual2::getProductName%',
                            ],
                            'data' => [
                                'selection_qty' => 1,
                                'selection_price_value' => 5,
                                'selection_price_type' => 'Fixed',
                            ]
                        ]
                    ]
                ],
                'bundle_option_1' => [
                    'title' => 'Radio Button Option',
                    'type' => 'Radio Buttons',
                    'required' => 'Yes',
                    'assigned_products' => [
                        [
                            'search_data' => [
                                'name' => '%item1_simple1::getProductName%',
                            ],
                            'data' => [
                                'selection_qty' => 1,
                                'selection_price_value' => 20,
                                'selection_price_type' => 'Fixed',
                            ]
                        ],
                        [
                            'search_data' => [
                                'name' => '%item1_virtual2::getProductName%',
                            ],
                            'data' => [
                                'selection_qty' => 1,
                                'selection_price_value' => 25,
                                'selection_price_type' => 'Fixed',
                            ]
                        ]
                    ]
                ],
                'bundle_option_2' => [
                    'title' => 'Checkbox Option',
                    'type' => 'Checkbox',
                    'required' => 'Yes',
                    'assigned_products' => [
                        [
                            'search_data' => [
                                'name' => '%item1_simple1::getProductName%',
                            ],
                            'data' => [
                                'selection_qty' => 1,
                                'selection_price_value' => 30,
                                'selection_price_type' => 'Fixed',
                            ]
                        ],
                        'assigned_products' => [
                            0 => [
                                'search_data' => [
                                    'name' => '%item1::getProductName%',
                                ],
                                'data' => [
                                    'selection_qty' => [
                                        'value' => 1
                                    ],
                                    'product_id' => [
                                        'value' => '%item1::getProductId%'
                                    ],
                                    'selection_price_value' => [
                                        'value' => '5'
                                    ]
                                ]
                            ],
                            1 => [
                                'search_data' => [
                                    'name' => '%item2::getProductName%',
                                ],
                                'data' => [
                                    'selection_qty' => [
                                        'value' => 1
                                    ],
                                    'product_id' => [
                                        'value' => '%item2::getProductId%'
                                    ],
                                    'selection_price_value' => [
                                        'value' => '10'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        if (!isset($presets[$name])) {
            throw new \InvalidArgumentException(
                sprintf('Wrong Bundle Selections preset name: %s', $name)
            );
        }
        return $presets[$name];
    }
}
