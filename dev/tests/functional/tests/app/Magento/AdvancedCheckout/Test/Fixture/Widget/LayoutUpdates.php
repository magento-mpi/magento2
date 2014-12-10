<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\AdvancedCheckout\Test\Fixture\Widget;

/**
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
            'order_by_sku_on_all_pages' => [
                [
                    'page_group' => 'Generic Pages/All Pages',
                    'block' => 'Sidebar Main',
                    'template' => 'Order by SKU Template',
                    'entities' => 'catalogCategory::default',
                ],
            ],
        ];

        if (!isset($presets[$name])) {
            return null;
        }

        return $presets[$name];
    }
}
