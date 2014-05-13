<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Fixture\Bundle;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class Price
 *
 * Data keys:
 *  - preset (Price verification preset name)
 *  - value (Price value)
 *
 */
class Price implements FixtureInterface
{
    /**
     * @var \Mtf\Fixture\FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * @var string
     */
    protected $currentPreset;

    /**
     * @param array $params
     * @param array $data
     */
    public function __construct(array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['value'])) {
            $this->data = $data['value'];
        }
        if (isset($data['preset'])) {
            $this->currentPreset = $data['preset'];
        }
    }

    /**
     * Persist custom selections products
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
     * @return array|null
     */
    public function getPreset()
    {
        $presets = [
            'MAGETWO-23066' => [
                'price_from' => '$115.00',
                'price_to' => '$120.00',
                'cart_price' => '$145.00'
            ],
            'MAGETWO-23069' => [
                'price_from' => '$115.00',
                'price_to' => '$120.00',
                'cart_price' => '$126.00'
            ],
            'MAGETWO-23070' => [
                'price_from' => '$40.00',
                'price_to' => '$100.00',
                'cart_price' => '$100.00'
            ],
            'MAGETWO-23061' => [
                'price_from' => '$32.00',
                'price_to' => '$80.00',
                'cart_price' => '$80.00'
            ]
        ];
        if (!isset($presets[$this->currentPreset])) {
            return null;
        }
        return $presets[$this->currentPreset];
    }
}
