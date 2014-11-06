<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Fixture\Widget;

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
            'banner_on_all_pages' => [
                [
                    'page_group' => ['Generic Pages', 'All Pages'],
                    'block' => 'Main Content Area',
                    'template' => 'Banner Block Template',
                ]
            ],
            'for_virtual_product' => [
                [
                    'page_group' => ['Products', 'Virtual Product'],
                    'for' => 'Yes',
                    'entities' => 'catalogProductVirtual::default',
                    'block' => 'Main Content Area',
                    'template' => 'Banner Block Template'
                ]
            ],
        ];

        if (!isset($presets[$name])) {
            return null;
        }

        return $presets[$name];
    }
}
