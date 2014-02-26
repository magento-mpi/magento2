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
 * Class Bundle
 *
 * Data keys:
 *  - preset (bundle options preset name)
 *  - products (comma separated sku identifiers)
 *
 * @package Magento\Bundle\Test\Fixture
 */
class Selections implements FixtureInterface
{
    /**
     * @var \Mtf\Fixture\FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param $data
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
                /** @var $product FixtureInterface */
                $product->persist();
            }
        }
    }

    /**
     * Return prepared data set
     *
     * @param $key [optional]
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
     * @param $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function getPreset($name)
    {
        $presets = [
            'default' => [
                'name' => 'Bundle Selections Default Preset',
                'items' => [
                    'bundle_item_0' => [
                        'title' => [
                            'value' => 'Drop-down Option'
                        ],
                        'type' => [
                            'value' => 'Drop-down',
                            'input_value' => 'select'
                        ],
                        'required' => [
                            'value' => 'Yes',
                            'input_value' => '1'
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
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'second' => [
                'name' => 'Bundle Selections Default Preset',
                'items' => [
                    'bundle_item_0' => [
                        'title' => [
                            'value' => 'Drop-down Second Option'
                        ],
                        'type' => [
                            'value' => 'Drop-down',
                            'input_value' => 'select'
                        ],
                        'required' => [
                            'value' => 'Yes',
                            'input_value' => '1'
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
