<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Block\Adminhtml\Review\Products\Viewed;

use Magento\Backend\Test\Block\GridPageActions;

/**
 * Class Action
 * Action block for Product Views Report
 */
class Action extends GridPageActions
{
    /**
     * Show Report button
     *
     * @var string
     */
    protected $showReportButton = '#filter_form_submit';

    /**
     * Show report button click
     *
     * @return void
     */
    public function showReport()
    {
        $this->_rootElement->find($this->showReportButton)->click();
    }
}
