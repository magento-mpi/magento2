<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable;

use Mtf\Fixture\FixtureInterface;

/**
 * Class ConfigurableOptions
 * Source options of the configurable products
 */
class ConfigurableOptions implements FixtureInterface
{
    /**
     * Source constructor
     *
     * @param array $params
     * @param array $dependenceData
     * @param array $data
     */
    public function __construct(array $params, array $dependenceData, array $data = [])
    {
        $dependenceData = $dependenceData['attribute_options'];
        $this->params = $params;
        if (isset($data['preset'])) {
            $this->data = $this->getPreset($data['preset']);
        }

        if (isset($this->data['id']) && $this->data['id'] === 'new') {
            $this->data['attribute_id'] = $dependenceData['id'];
            $this->data['code'] = $dependenceData['attribute_code'];
            $this->data['label'] = $dependenceData['frontend_label'];
            foreach ($this->data['values'] as &$value) {
                $data = array_shift($dependenceData['option']['value']);
                $value['value_index'] = $data['id'];
            }
            unset($value);
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
     * @param string|null $key
     * @return mixed
     */
    public function getData($key = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $this->data;
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
            'attributes_data' => [
                'value' => [
                    'label' => [
                        'value' => 'test%isolation%'
                    ],
                    [
                        'option_label' => [
                            'value' => 'option 0'
                        ],
                        'pricing_value' => [
                            'value' => 30.00
                        ],
                        'is_percent' => [
                            'value' => 'No'
                        ],
                        'include' => [
                            'value' => 'Yes'
                        ],
                    ],
                    [
                        'option_label' => [
                            'value' => 'option 1'
                        ],
                        'pricing_value' => [
                            'value' => 40.00
                        ],
                        'is_percent' => [
                            'value' => 'No'
                        ],
                        'include' => [
                            'value' => 'Yes'
                        ],
                    ]
                ]
            ],
            'default' => [
                'id' => 'new',
                'values' => [
                    [
                        'pricing_value' => 100.00,
                        'is_percent' => 'No',
                        'include' => 'Yes'
                    ],
                    [
                        'pricing_value' => 200.00,
                        'is_percent' => 'No',
                        'include' => 'Yes'
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
