<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CatalogEvent\Test\Block;

use Magento\Widget\Test\Fixture\Widget;

/**
 * Widget catalog event block on the frontend
 */
class WidgetView extends \Magento\Widget\Test\Block\WidgetView
{
    /**
     * Widgets selectors
     *
     * @var array
     */
    protected $widgetSelectors = [
        'catalogEventCarousel' => '(.//*/a/span[contains(.,"%s")])[last()]',
    ];
}
