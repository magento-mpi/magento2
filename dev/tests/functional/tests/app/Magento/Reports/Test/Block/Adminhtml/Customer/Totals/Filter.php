<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Block\Adminhtml\Customer\Totals;

use Mtf\ObjectManager;
use Magento\Reports\Test\Block\Adminhtml\AbstractFilter;

/**
 * Class Filter
 * Filter for Order Total Report
 */
class Filter extends AbstractFilter
{
    /**
     * Skipped fields
     *
     * @var array
     */
    protected $skippedFields = ['report_period'];

    /**
     * Refresh button css selector
     *
     * @var string
     */
    protected $refresh = '[data-ui-id="adminhtml-report-grid-refresh-button"]';

    /**
     * Click refresh filter button
     *
     * @return void
     */
    public function refreshFilter()
    {
        $this->_rootElement->find($this->refresh)->click();
    }
}
