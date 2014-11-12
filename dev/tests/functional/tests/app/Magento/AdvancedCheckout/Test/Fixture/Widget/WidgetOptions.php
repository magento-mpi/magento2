<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Test\Fixture\Widget;

use Magento\Widget\Test\Fixture\Widget\WidgetOptions as AbstractWidgetOptions;

/**
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
            'orderBySku' => [
                [
                    'link_display' => 'Yes',
                    'link_text' => 'text%isolation%'
                ]
            ],
        ];

        if (!isset($presets[$name])) {
            return null;
        }

        return $presets[$name];
    }
}
