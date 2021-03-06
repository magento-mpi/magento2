<?php
/**
 * @spi
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerSegment\Test\Block\Adminhtml\Customersegment;

use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;

/**
 * Class Grid
 * Backend customer segment grid
 */
class Grid extends AbstractGrid
{
    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = 'td[class*=col-grid_segment_name]';

    /**
     * First row selector
     *
     * @var string
     */
    protected $firstRowSelector = '//tr[./td[contains(@class, "col-grid_segment_name")]][1]';

    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'grid_segment_name' => [
            'selector' => 'input[name="grid_segment_name"]',
        ],
        'grid_segment_is_active' => [
            'selector' => 'select[name="grid_segment_is_active"]',
            'input' => 'select',
        ],
        'grid_segment_website' => [
            'selector' => 'select[name="grid_segment_website"]',
            'input' => 'select',
        ],
    ];
}
