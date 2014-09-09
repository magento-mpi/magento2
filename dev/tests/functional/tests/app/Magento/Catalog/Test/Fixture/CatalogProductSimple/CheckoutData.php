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
        $preset = isset($data['preset']) ? $this->getPreset($data['preset']) : [];
        $this->data = $preset ? $preset : [];

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
     * @param int $key [optional]
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
            'MAGETWO-23062' => [
                'custom_options' => [
                    [
                        'title' => 0,
                        'value' => 0
                    ]
                ]
            ],
            'MAGETWO-23063' => [
                'custom_options' => [
                    [
                        'title' => 0,
                        'value' => 0
                    ]
                ]
            ],
            'options-suite' => [
                'custom_options' => [
                    [
                        'title' => 0,
                        'value' => 'Field value 1 %isolation%'
                    ],
                    [
                        'title' => 1,
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
            ],
            'order_default' => [
                'qty' => 2
            ]
        ];
        return isset($presets[$name]) ? $presets[$name] : null;
    }
}
