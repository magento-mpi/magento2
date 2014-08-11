<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
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
    protected $firstRowSelector = '[data-role="row"] td[data-column="grid_segment_id"]';

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

    /**
     * Check whether first row is visible
     *
     * @return bool
     */
    public function isFirstRowVisible()
    {
        return $this->_rootElement->find($this->firstRowSelector)->isVisible();
    }

    /**
     * Open first item in grid
     *
     * @return void
     */
    public function openFirstRow()
    {
        $this->_rootElement->find($this->firstRowSelector)->click();
    }
}
