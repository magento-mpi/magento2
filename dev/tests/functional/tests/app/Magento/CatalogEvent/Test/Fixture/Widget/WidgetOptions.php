<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CatalogEvent\Test\Fixture\Widget;

/**
 * Class WidgetOptions
 * Prepare Widget options for widget
 */
class WidgetOptions extends \Magento\Widget\Test\Fixture\Widget\WidgetOptions
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
                ],
            ],
        ];

        if (!isset($presets[$name])) {
            return null;
        }

        return $presets[$name];
    }
}
