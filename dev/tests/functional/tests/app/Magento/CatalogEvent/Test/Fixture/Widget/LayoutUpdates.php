<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Fixture\Widget;

/**
 * Class LayoutUpdates
 * Prepare Layout Updates for widget
 */
class LayoutUpdates extends \Magento\Widget\Test\Fixture\Widget\LayoutUpdates
{
    /**
     * Preset for Layout Updates
     *
     * @param string $name
     * @return array|null
     */
    protected function getPreset($name)
    {
        $presets = [
            'for_event_carousel' => [
                [
                    'page_group' => ['Generic Pages', 'All Pages'],
                    'block' => 'Main Content Area',
                ]
            ],
        ];

        if (!isset($presets[$name])) {
            return null;
        }

        return $presets[$name];
    }
}
