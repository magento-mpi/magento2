<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Banner\Test\Block;

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
    protected $widgetSelectors = ['bannerRotator' => '/descendant-or-self::div[contains(.,"%s")]'];
}
