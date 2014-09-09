<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Fixture\CatalogProductSimple;

use Mtf\Fixture\FixtureInterface;

/**
 * Class CheckoutData
 * Data for fill product form on frontend
 *
 * Data keys:
 *  - preset (Checkout data verification preset name)
 */
class CheckoutData implements FixtureInterface
{
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
    protected $data;

    /**
     * @constructor
     * @param array $params
     * @param array $data
     */
    public function __construct(array $params, array $data = [])
    {
        $this->params = $params;
        $this->data = isset($data['preset']) ? $this->getPreset($data['preset']) : [];

        if (isset($data['value'])) {
            $this->data = array_replace_recursive($this->data, $data['value']);
        }
    }

    /**
     * Persist custom selections products
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data set
     *
     * @param string $key [optional]
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
     * Return array preset
     *
     * @param string $name
     * @return array|null
     */
    protected function getPreset($name)
    {
        $presets = [
            'with_two_custom_option' => [
                'options' => [
                    'custom_options' => [
                        [
                            'title' => 'attribute_key_0',
                            'value' => 'option_key_0'
                        ],
                        [
                            'title' => 'attribute_key_1',
                            'value' => 'Content option %isolation%',
                        ]
                    ]
                ],
                'cartItem' => [
                    'price' => 340,
                    'qty' => 1,
                    'subtotal' => 340
                ]
            ],
            'options-suite' => [
                'options' => [
                    'custom_options' => [
                        [
                            'title' => 'attribute_key_0',
                            'value' => 'Field value 1 %isolation%'
                        ],
                        [
                            'title' => 'attribute_key_1',
                            'value' => 'Field value 2 %isolation%'
                        ],
                        [
                            'title' => 2,
                            'value' => 1
                        ],
                        [
                            'title' => 3,
                            'value' => 0
                        ]
                    ]
                ]
            ],
            'MAGETWO-23062' => [
                'options' => [
                    'custom_options' => [
                        [
                            'title' => 0,
                            'value' => 0
                        ]
                    ]
                ]
            ],
            'MAGETWO-23063' => [
                'options' => [
                    'custom_options' => [
                        [
                            'title' => 0,
                            'value' => 0
                        ]
                    ]
                ]
            ],
        ];
        return isset($presets[$name]) ? $presets[$name] : null;
    }
}
