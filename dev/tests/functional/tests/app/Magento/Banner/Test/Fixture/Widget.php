<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Fixture;

use Magento\Widget\Test\Fixture\Widget as ParentWidget;

/**
 * Class Widget
 * Fixture for Widget
 */
class Widget extends ParentWidget
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Banner\Test\Repository\Widget';

    protected $widgetOptions = [
        'attribute_code' => 'widgetOptions',
        'backend_type' => 'virtual',
        'source' => 'Magento\Banner\Test\Fixture\Widget\WidgetOptions',
        'group' => 'banner_options',
    ];

    protected $layout = [
        'attribute_code' => 'layout',
        'backend_type' => 'virtual',
        'source' => 'Magento\Banner\Test\Fixture\Widget\LayoutUpdates',
        'group' => 'layout_updates',
    ];
}
