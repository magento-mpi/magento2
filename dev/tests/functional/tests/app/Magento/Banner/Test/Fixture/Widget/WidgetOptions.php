<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Fixture\Widget;

use Mtf\Fixture\FixtureFactory;

/**
 * Class WidgetOptions
 * Prepare Widget options for widget
 */
class WidgetOptions extends \Magento\Widget\Test\Fixture\Widget\WidgetOptions
{
    /**
     * Constructor
     *
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        parent::__construct($fixtureFactory, $params, $data);

        $this->data[0]['name'] = 'bannerRotator';
    }

    /**
     * Preset for Widget options
     *
     * @param string $name
     * @return array|null
     */
    protected function getPreset($name)
    {
        $presets = [
            'bannerRotator' => [
                [
                    'display_mode' => 'Specified Banners',
                    'rotate' => 'Display all instead of rotating.',
                    'entities' => ['bannerInjectable::default'],
                ]
            ],
            'bannerRotatorShoppingCartRules' => [
                [
                    'display_mode' => 'Specified Banners',
                    'rotate' => 'Display all instead of rotating.',
                    'entities' => ['bannerInjectable::banner_rotator_shopping_cart_rules'],
                ]
            ],
            'bannerRotatorCatalogRules' => [
                [
                    'display_mode' => 'Specified Banners',
                    'rotate' => 'Display all instead of rotating.',
                    'entities' => ['bannerInjectable::banner_rotator_catalog_rules'],
                ]
            ],
        ];

        if (!isset($presets[$name])) {
            return null;
        }

        return $presets[$name];
    }
}
