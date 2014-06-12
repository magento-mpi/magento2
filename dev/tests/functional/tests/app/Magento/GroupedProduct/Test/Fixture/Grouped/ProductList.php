<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Fixture\Grouped;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class ProductList
 * Grouped selections preset
 */
class ProductList implements FixtureInterface
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
     * @param array $params
     * @param bool $persist
     */
    public function __construct(
        FixtureFactory $fixtureFactory,
        $data,
        array $params = [],
        $persist = false
    ) {
        $this->data = $data;

        if (isset($this->data['products'])) {
            $products = explode(',', $this->data['products']);
            $this->data['products'] = [];
            foreach ($products as $key => $product) {
                list($fixture, $dataSet) = explode('::', $product);
                $this->data['products'][$key] = $fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
            }
        }
        if (isset($this->data['preset'])) {
            $this->currentPreset = $this->data['preset'];
            $this->data['preset'] = $this->getPreset($this->data['preset']);
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
                        'search_data' => [
                            'sku' => '%item1_simple::getProductSku%',
                        ],
                        'qty' => 5,
                    ],
                    [
                        'search_data' => [
                            'sku' => '%item2_simple::getProductSku%',
                        ],
                        'qty' => 6,
                    ],
                ]
            ],
            'defaultVirtualProduct' => [
                'assigned_products' => [

                    [
                        'search_data' => [
                            'sku' => '%item1_virtual::getProductSku%',
                        ],
                        'qty' => 5,
                    ],
                    [
                        'search_data' => [
                            'sku' => '%item2_virtual::getProductSku%',
                        ],
                        'qty' => 3,
                    ],

                ]
            ]
        ];
        if (!isset($presets[$name])) {
            return null;
        }
        return $presets[$name];
    }
}
