<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Fixture\CatalogProductSimple;

use Mtf\Fixture\FixtureInterface;

/**
 * Class TierPriceOptions
 *
 * Data keys:
 *  - preset (Price options preset name)
 *  - products (comma separated sku identifiers)
 *
 */
class TierPriceOptions implements FixtureInterface
{
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
     * Persist group price
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
     * @param string $name
     * @return mixed|null
     */
    protected function getPreset($name)
    {
        $presets = [
            'default' => [
                0 => [
                    'price' => 150,
                    'website' => 'All Websites [USD]',
                    'price_qty' => 3,
                    'customer_group' => 'ALL GROUPS'
                ],
                1 => [
                    'price' => 24,
                    'website' => 'All Websites [USD]',
                    'price_qty' => 15,
                    'customer_group' => 'ALL GROUPS'
                ]
            ],
            'MAGETWO-23002' => [
                0 => [
                    'price' => 90,
                    'website' => 'All Websites [USD]',
                    'price_qty' => 2,
                    'customer_group' => 'ALL GROUPS'
                ]
            ]
        ];

        if (!isset($presets[$name])) {
            return null;
        }
        return $presets[$name];
    }
}
