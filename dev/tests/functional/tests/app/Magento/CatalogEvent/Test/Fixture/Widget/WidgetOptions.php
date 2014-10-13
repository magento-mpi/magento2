<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Fixture\Widget;

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
            'catalogEventCarousel' => [
                [
                    'limit' => '6',
                    'scroll' => '3',
                    'entities' => 'catalogEventEntity::default_event::2',
                ]
            ],
        ];

        if (!isset($presets[$name])) {
            return null;
        }

        return $presets[$name];
    }
}
