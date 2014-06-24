<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Fixture\CatalogProductGrouped;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class GroupedProducts
 * Grouped selections preset
 */
class GroupedProducts implements FixtureInterface
{
    /**
     * Current preset
     *
     * @var string
     */
    protected $currentPreset;

    /**
     * Constructor
     *
     * @param FixtureFactory $fixtureFactory
     * @param array $data
     * @param array $params [optional]
     * @param bool $persist [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, $data, array $params = [], $persist = false)
    {
        $this->params = $params;

        if ($data['preset']) {
            $this->currentPreset = $data['preset'];
            $this->data = $this->getPreset($this->currentPreset);
        }

        if (!empty($this->data['products'])) {
            foreach ($this->data['products'] as $key => $product) {
                list($fixture, $dataSet) = explode('::', $product);
                $this->data['products'][$key] = $fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
            }
        }

        $this->params = $params;
        if ($persist) {
            $this->persist();
        }
    }

    /**
     * Persist grouped selections products
     *
     * @return void
     */
    public function persist()
    {
        if (!empty($this->data['products'])) {
            foreach ($this->data['products'] as $product) {
                /** @var $product FixtureInterface */
                $product->persist();
            }

            $assignedProducts = & $this->data['assigned_products'];
            foreach (array_keys($assignedProducts) as $key) {
                $assignedProducts[$key]['id'] = $this->data['products'][$key]->getId();
                $assignedProducts[$key]['position'] = $key + 1;
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
     * Preset array
     *
     * @param string $name
     * @return mixed|null
     */
    protected function getPreset($name)
    {
        $presets = [
            'defaultSimpleProduct' => [
                'assigned_products' => [
                    [
                        'id' => '%id%',
                        'position' => '%position%',
                        'qty' => 5
                    ],
                    [
                        'id' => '%id%',
                        'position' => '%position%',
                        'qty' => 6
                    ]
                ],
                'products' => [
                    'catalogProductSimple::default',
                    'catalogProductSimple::default'
                ],
            ],
            'defaultVirtualProduct' => [
                'assigned_products' => [
                    [
                        'id' => '%id%',
                        'position' => '%position%',
                        'qty' => 5
                    ],
                    [
                        'id' => '%id%',
                        'position' => '%position%',
                        'qty' => 6
                    ]
                ],
                'products' => [
                    'catalogProductVirtual::default',
                    'catalogProductVirtual::default'
                ],
            ]
        ];

        if (!isset($presets[$name])) {
            return null;
        }

        return $presets[$name];
    }
}
