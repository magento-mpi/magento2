<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Block\Adminhtml\Report\Customer\Segment;

use Magento\Backend\Test\Block\Widget\Grid as WidgetGrid;

/**
 * Class ReportGrid
 * Customer segment report grid
 */
class ReportGrid extends WidgetGrid
{
    /**
     * CSS selector grid mass action form
     *
     * @var string
     */
    protected $gridActionBlock = '#gridReportCustomersegments_massaction';

    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'segment_id' => [
            'selector' => 'input[name="segment_id"]'
        ],
        'name' => [
            'selector' => 'input[name="name"]'
        ],
        'is_active' => [
            'selector' => 'select[name="is_active"]',
            'input' => 'select'
        ],
        'website' => [
            'selector' => 'select[name="website"]',
            'input' => 'select'
        ],
        'customer_count' => [
            'selector' => 'input[name="customer_count"]'
        ],
    ];

    /**
     * Getting grid action form
     *
     * @return \Magento\CustomerSegment\Test\Block\Adminhtml\Report\Customer\Segment\Grid\Massaction
     */
    public function getGridActions()
    {
        return $this->blockFactory->create(
            'Magento\CustomerSegment\Test\Block\Adminhtml\Report\Customer\Segment\Grid\Massaction',
            ['element' => $this->_rootElement->find($this->gridActionBlock)]
        );
    }
}
