<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\VersionsCms\Test\Block;

use Magento\Widget\Test\Fixture\Widget;

/**
 * Widget view block on the index page
 */
class WidgetView extends \Magento\Widget\Test\Block\WidgetView
{
    /**
     * Widgets selectors
     *
     * @var array
     */
    protected $widgetSelectors = [
        'hierarchyNodeLink' => './/*/a[contains(.,"%s")]',
    ];
}
