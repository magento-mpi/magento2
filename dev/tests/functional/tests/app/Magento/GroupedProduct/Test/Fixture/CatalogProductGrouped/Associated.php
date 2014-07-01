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
use Mtf\Fixture\InjectableFixture;

/**
 * Class Associated
 * Grouped selections preset
 */
class Associated implements FixtureInterface
{
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
        $this->params = $params;

        if ($data['preset']) {
            $this->currentPreset = $data['preset'];
            $this->data = $this->getPreset($this->currentPreset);
        }

        $data['products'] = explode(',', $data['products']);
        if (!empty($data['products'])) {
            foreach ($data['products'] as $key => $product) {
                list($fixture, $dataSet) = explode('::', $product);
                /** @var $productFixture InjectableFixture */
                $productFixture = $fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
                if (!$productFixture->hasData('id')) {
                    $productFixture->persist();
                }

                $this->data['products'][$key] = $productFixture;
            }

            $assignedProducts = & $this->data['assigned_products'];
            foreach (array_keys($assignedProducts) as $key) {
                $assignedProducts[$key]['name'] = $this->data['products'][$key]->getName();
                $assignedProducts[$key]['id'] = $this->data['products'][$key]->getId();
                $assignedProducts[$key]['position'] = $key + 1;
            }
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
                        'id' => '%id%',
                        'name' => '%item1_simple::getProductName%',
                        'position' => '%position%',
                        'qty' => 5,
                    ],
                    [
                        'id' => '%id%',
                        'name' => '%item1_simple::getProductName%',
                        'position' => '%position%',
                        'qty' => 33,
                    ],
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
                        'name' => '%item1_virtual::getProductName%',
                        'position' => '%position%',
                        'qty' => 5,
                    ],
                    [
                        'id' => '%id%',
                        'name' => '%item1_virtual::getProductName%',
                        'position' => '%position%',
                        'qty' => 3,
                    ],
                ],
                'products' => [
                    'catalogProductSimple::default',
                    'catalogProductSimple::default'
                ],
            ]
        ];
        if (!isset($presets[$name])) {
            return null;
        }
        return $presets[$name];
    }
}
