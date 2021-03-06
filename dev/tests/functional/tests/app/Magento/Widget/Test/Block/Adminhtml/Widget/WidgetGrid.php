<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget;

use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;

/**
 * Widget grid on the Widget Instance Index page
 */
class WidgetGrid extends AbstractGrid
{
    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = 'tbody tr td.col-title';

    /**
     * First row selector
     *
     * @var string
     */
    protected $firstRowSelector = '//tr[./td[contains(@class, "col-title")]][1]/td';

    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'title' => [
            'selector' => 'input[name="title"]',
        ],
    ];
}
