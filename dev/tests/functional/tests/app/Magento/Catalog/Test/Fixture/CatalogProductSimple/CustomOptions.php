<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Fixture\CatalogProductSimple;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class CustomOptions
 *
 * Data keys:
 *  - preset (bundle options preset name)
 *  - products (comma separated sku identifiers)
 *
 * @package Magento\Bundle\Test\Fixture
 */
class CustomOptions implements FixtureInterface
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
     * Persist bundle selections products
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
     * @param $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function getPreset($name)
    {
        $presets = [
            'default' => [
                [
                    'title' => 'custom option drop down',
                    'is_require' => true,
                    'type' => 'Drop-down',
                    'options' => [
                        [
                            'title' => '30 bucks',
                            'price' => 30,
                            'price_type' => 'Fixed',
                            'sku' => 'sku_drop_down_row_1'
                        ]
                    ]
                ]
            ]
        ];
        if (!isset($presets[$name])) {
            throw new \InvalidArgumentException(
                sprintf('Wrong Bundle Selections preset name: %s', $name)
            );
        }
        return $presets[$name];
    }
}
