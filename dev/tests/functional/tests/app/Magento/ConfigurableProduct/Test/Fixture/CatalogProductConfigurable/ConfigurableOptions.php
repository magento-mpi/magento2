<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class ConfigurableOptions
 *
 * Data keys:
 *  - preset (Configurable options preset name)
 *  - products (comma separated sku identifiers)
 *
 * @package Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable
 */
class ConfigurableOptions implements FixtureInterface
{
    /**
     * @var \Mtf\Fixture\FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * @param array $params
     * @param array $data
     */
    public function __construct(array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['preset'])) {
            $this->data = $this->getPreset($data['preset']);
        }
    }

    /**
     * Persist configurable product options
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
     * @param string $name
     * @return array|null
     */
    protected function getPreset($name)
    {
        $presets = [
            'configurable_attributes_data' => [
                'value' => [
                    'label' => [
                        'value' => 'test%isolation%'
                    ],
                    '0' => [
                        'option_label' => [
                            'value' => 'option 0'
                        ],
                        'pricing_value' => [
                            'value' => '30'
                        ],
                        'is_percent' => [
                            'value' => 'No'
                        ],
                        'include' => [
                            'value' => 'Yes'
                        ],
                    ],
                    '1' => [
                        'option_label' => [
                            'value' => 'option 1'
                        ],
                        'pricing_value' => [
                            'value' => '40'
                        ],
                        'is_percent' => [
                            'value' => 'No'
                        ],
                        'include' => [
                            'value' => 'Yes'
                        ],
                    ]
                ]
            ]
        ];
        if (!isset($presets[$name])) {
            return null;
        }
        return $presets[$name];
    }
}
