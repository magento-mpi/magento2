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

        $products = '';
        if (isset($data['products'])) {
            $products = $data['products'];
            $products = explode(',', $products);
        } elseif (isset($data['preset'])) {
            $products = $this->getPreset($data['preset'])['products'];
        }

        foreach ($products as $product) {
            list($fixture, $dataSet) = explode('::', $product);
            $product = $fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
            if ($product->hasData('id') === false) {
                $product->persist();
            }
            $this->data['products'][] = $product;
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
                'products' => [
                    'catalogProductSimple::default',
                ]
            ],
        ];
        if (!isset($presets[$name])) {
            throw new \InvalidArgumentException(
                sprintf('Wrong Order preset name: %s', $name)
            );
        }
        return $presets[$name];
    }
}
