<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class CustomerSegmentReportIndex
 */
class CustomerSegmentReportIndex extends BackendPage
{
    const MCA = 'customersegment/report_customer_customersegment/segment';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'pageActionsBlock' => [
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'reportGrid' => [
            'class' => 'Magento\CustomerSegment\Test\Block\Adminhtml\Report\Customer\Segment\ReportGrid',
            'locator' => '#gridReportCustomersegments',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '.messages .messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getPageActionsBlock()
    {
        return $this->getBlockInstance('pageActionsBlock');
    }

    /**
     * @return \Magento\CustomerSegment\Test\Block\Adminhtml\Report\Customer\Segment\ReportGrid
     */
    public function getReportGrid()
    {
        return $this->getBlockInstance('reportGrid');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}
