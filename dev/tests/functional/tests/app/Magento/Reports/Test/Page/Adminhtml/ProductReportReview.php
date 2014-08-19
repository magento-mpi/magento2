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
 * Class ProductReportReview
 * Product reviews report page
 */
class ProductReportReview extends BackendPage
{
    const MCA = 'reports/report_review/product';

    /**
     * @var array
     */
    protected $_blocks = [
        'gridBlock' => [
            'name' => 'gridBlock',
            'class' => 'Magento\Reports\Test\Block\Adminhtml\Review\Products\Grid',
            'locator' => '#gridProducts',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Reports\Test\Block\Adminhtml\Review\Products\Grid
     */
    public function getGridBlock()
    {
        return $this->getBlockInstance('gridBlock');
    }
}
