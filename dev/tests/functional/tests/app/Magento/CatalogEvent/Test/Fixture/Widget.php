<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Fixture;

use Magento\Widget\Test\Fixture\Widget as ParentWidget;

/**
 * Class Widget
 * Fixture for Widget
 */
class Widget extends ParentWidget
{
    protected $widgetOptions = [
        'attribute_code' => 'widgetOptions',
        'backend_type' => 'virtual',
        'source' => 'Magento\CatalogEvent\Test\Fixture\Widget\WidgetOptions',
        'group' => 'catalog_event_widget_options',
    ];
}
