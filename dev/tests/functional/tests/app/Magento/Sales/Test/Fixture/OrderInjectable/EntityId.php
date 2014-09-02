<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Fixture\OrderInjectable;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class EntityId
 * EntityId preset
 */
class EntityId implements FixtureInterface
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
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $data
     * @param array $params [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, array $data, array $params = [])
    {
        $this->params = $params;

        if (isset($data['value'])) {
            $this->data = $data['value'];
            return;
        }

        if (isset($data['preset'])) {
            $this->currentPreset = $data['preset'];
            $this->data = $this->getPreset($this->currentPreset);
            if (!empty($data['products'])) {
                $this->data['products'] = explode(',', $data['products']);
            }
        }

        if (!empty($this->data['products'])) {
            $productsSelections = $this->data['products'];
            $this->data['products'] = [];
            foreach ($productsSelections as $index => $product) {
                list($fixture, $dataSet) = explode('::', $product);
                $productSelection[$index] = $fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
                $productSelection[$index]->persist();
                $this->data['data'][$index]['name'] = $productSelection[$index]->getName();
                $this->data['products'] = $productSelection;
            }
        }
    }

    /**
     * Persist order products
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
     * Preset array
     *
     * @param string $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function getPreset($name)
    {
        $presets = [
            'default' => [
                'data' => [
                    [
                        'name' => '%product_name%',
                        'qty' => 2,
                        'use_discount' => ''
                    ],
                ],
                'products' => [
                    'catalogProductSimple::default',
                ]
            ],
            'default_configurable' => [
                'data' => [
                    [
                        'name' => '%product_name%',
                        'qty' => 2,
                        'use_discount' => '',
                        'super_attribute' => '',
                    ],
                ],
                'products' => [
                    'catalogProductSimple::default',
                ]
            ]
        ];
        if (!isset($presets[$name])) {
            throw new \InvalidArgumentException(
                sprintf('Wrong Order preset name: %s', $name)
            );
        }
        return $presets[$name];
    }
}
