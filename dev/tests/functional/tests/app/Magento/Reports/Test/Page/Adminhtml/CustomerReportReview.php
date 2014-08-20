<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class CustomerReportReview
 * Customer Report Review page
 */
class CustomerReportReview extends BackendPage
{
    const MCA = 'reports/report_review/customer';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'gridBlock' => [
            'class' => 'Magento\Reports\Test\Block\Adminhtml\Review\Customer\Grid',
            'locator' => '#customers_grid',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Reports\Test\Block\Adminhtml\Review\Customer\Grid
     */
    public function getGridBlock()
    {
        return $this->getBlockInstance('gridBlock');
    }
}
