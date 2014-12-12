<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\VersionsCms\Test\Fixture;

/**
 * Fixture for Widget Cms Hierarchy
 */
class Widget extends \Magento\Widget\Test\Fixture\Widget
{
    protected $widgetOptions = [
        'attribute_code' => 'widgetOptions',
        'backend_type' => 'virtual',
        'source' => 'Magento\VersionsCms\Test\Fixture\Widget\WidgetOptions',
        'group' => 'hierarchy_node_link_widget_options',
    ];
}
