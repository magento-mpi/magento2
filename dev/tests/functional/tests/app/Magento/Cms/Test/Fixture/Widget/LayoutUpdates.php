<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Fixture\Widget;

use Magento\Widget\Test\Fixture\Widget\LayoutUpdates as AbstractLayoutUpdates;

/**
 * Class LayoutUpdates
 * Prepare Layout Updates for widget
 */
class LayoutUpdates extends AbstractLayoutUpdates
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
            'for_cms_hierarchy' => [
                [
                    'page_group' => 'All Pages',
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
