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
     * Prepared dataSet data
     *
     * @var array
     */
    protected $data;

    /**
     * Data set configuration settings
     *
     * @var array
     */
    protected $params;

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
     */
    public function __construct(FixtureFactory $fixtureFactory, array $data, array $params = [])
    {
        $this->params = $params;

        if ($data['preset']) {
            $this->currentPreset = $data['preset'];
            $this->data = $this->getPreset($this->currentPreset);
        }

        if (!empty($this->data['products'])) {
            $this->data['products'] = is_array($this->data['products'])
                ? $this->data['products']
                : explode(',', $this->data['products']);
            foreach ($this->data['products'] as $key => $product) {
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
     * @param string|null $key [optional]
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
                        'qty' => 1,
                    ],
                    [
                        'id' => '%id%',
                        'name' => '%item1_simple::getProductName%',
                        'position' => '%position%',
                        'qty' => 2,
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
                        'qty' => 1,
                    ],
                    [
                        'id' => '%id%',
                        'name' => '%item1_virtual::getProductName%',
                        'position' => '%position%',
                        'qty' => 2,
                    ],
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
