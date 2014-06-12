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
 * Class VariationsMatrix
 * Related product in variations matrix
 */
class VariationsMatrix implements FixtureInterface
{
    /**
     * Isolation for unique names
     */
    const ISOLATION_PLACEHOLDER = '%isolation%';

    /**
     * Isolation for unique names
     */
    const OPTION_ID_PLACEHOLDER = '%option_id%';

    /**
     * Data set resource
     *
     * @var array
     */
    protected $data;

    /**
     * Source constructor
     *
     * @param int $isolation
     * @param array $params
     * @param array $dependenceData
     * @param array $data
     */
    public function __construct($isolation, array $params, array $dependenceData, array $data = [])
    {
        $dependenceData = $dependenceData['configurable_options'];
        $this->params = $params;
        if (isset($data['preset'])) {
            $this->data = $this->getPreset($data['preset']);
        }
        $placeholder = self::ISOLATION_PLACEHOLDER;
        array_walk_recursive(
            $this->data,
            function (&$item, $key, $placeholder) use ($isolation) {
                $item = str_replace($placeholder, $isolation, $item);
            },
            $placeholder
        );
        $productInMatrix = [];
        foreach ($dependenceData['values'] as $key => $option) {
            if (isset($this->data[$key])) {
                $productInMatrix[$option['value_index']]['name'] = str_replace(
                    self::OPTION_ID_PLACEHOLDER,
                    $option['value_index'],
                    $this->data[$key]['name']
                );
                $productInMatrix[$option['value_index']]['sku'] = str_replace(
                    self::OPTION_ID_PLACEHOLDER,
                    $option['value_index'],
                    $this->data[$key]['sku']
                );
                $productInMatrix[$option['value_index']]['quantity_and_stock_status']['qty'] = $this->data[$key]['qty'];
                $productInMatrix[$option['value_index']]['weight'] = $this->data[$key]['weight'];
                $productInMatrix[$option['value_index']]['configurable_attribute'] = sprintf(
                    $this->data[$key]['configurable_attribute'],
                    $dependenceData['code'],
                    $option['value_index']
                );
            }
        }

        $this->data = $productInMatrix;
    }

    /**
     * Persists prepared data into application
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
     * @return array
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
            'default' => [
                [
                    'name' => '%option_id%-product%isolation% in matrix',
                    'configurable_attribute' => '{"%s":"%d"}',
                    'sku' => 'sku_%option_id%_product%isolation%_in_matrix',
                    'qty' => 120,
                    'weight' => 12
                ],
                [
                    'name' => '%option_id%-product%isolation% in matrix',
                    'configurable_attribute' => '{"%s":"%d"}',
                    'sku' => 'sku_%option_id%_product%isolation%_in_matrix',
                    'qty' => 120,
                    'weight' => 12
                ]
            ]
        ];
        if (!isset($presets[$name])) {
            return null;
        }

        return $presets[$name];
    }
}
