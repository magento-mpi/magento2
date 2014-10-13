<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Fixture\Widget;

use Magento\Widget\Test\Fixture\Widget\WidgetOptions as AbstractWidgetOptions;

/**
 * Class WidgetOptions
 * Prepare Widget options for widget
 */
class WidgetOptions extends AbstractWidgetOptions
{
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
                    'entities' => 'bannerInjectable::default',
                ]
            ],
            'bannerRotatorShoppingCartRules' => [
                [
                    'display_mode' => 'Specified Banners',
                    'rotate' => 'Display all instead of rotating.',
                    'entities' => 'bannerInjectable::banner_rotator_shopping_cart_rules',
                ]
            ],
            'bannerRotatorCatalogRules' => [
                [
                    'display_mode' => 'Specified Banners',
                    'rotate' => 'Display all instead of rotating.',
                    'entities' => 'bannerInjectable::banner_rotator_catalog_rules',
                ]
            ],
        ];

        if (!isset($presets[$name])) {
            return null;
        }

        return $presets[$name];
    }
}
