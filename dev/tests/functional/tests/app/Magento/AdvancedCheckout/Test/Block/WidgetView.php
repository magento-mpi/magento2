<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Test\Block;

use Magento\Widget\Test\Fixture\Widget;

/**
 * Widget block on the frontend
 */
class WidgetView extends \Magento\Widget\Test\Block\WidgetView
{
    /**
     * Widgets selectors
     *
     * @var array
     */
    protected $widgetSelectors = ['orderBySku' => './/*/a[contains(.,"%s")]'];
}
