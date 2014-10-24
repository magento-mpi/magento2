<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Fixture\Widget;

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
            'hierarchyNodeLink' => [
                [
                    'anchor_text' => 'CustomText_%isolation%',
                    'title' => 'CustomTitle_%isolation%',
                    'node' => '%node_name%',
                    'entities' => ['cmsHierarchy::cmsHierarchy']
                ]
            ],
        ];

        if (!isset($presets[$name])) {
            return null;
        }

        return $presets[$name];
    }
}
