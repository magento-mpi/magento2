<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Fixture;

/**
 * Fixture for Banner Rotator.
 */
class Widget extends \Magento\Widget\Test\Fixture\Widget
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Banner\Test\Repository\Widget';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Banner\Test\Handler\Widget\WidgetInterface';

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
