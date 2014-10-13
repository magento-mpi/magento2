<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block\Adminhtml\Widget\Instance\Edit\Tab;

use Mtf\Client\Element;
use Magento\Widget\Test\Fixture\Widget;
use Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\WidgetOptions as AbstractWidgetOptions;

/**
 * Class LayoutUpdates
 * Widget options form
 */
class WidgetOptions extends AbstractWidgetOptions
{
    /**
     * Prepare class name
     *
     * @param string $widgetOptionsName
     * @return array
     */
    protected function optionNameConvert($widgetOptionsName)
    {
        return ['module' => 'Cms', 'name' => ucfirst($widgetOptionsName)];
    }
}
