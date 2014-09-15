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

    /**
     * Prepare data
     *
     * @param array $viewsReport
     * @return array
     */
    protected function prepareData(array $viewsReport)
    {
        foreach ($viewsReport as $name => $reportFilter) {
            if ($name === 'report_period') {
                continue;
            }
            $date = ObjectManager::getInstance()->create(
                '\Magento\Backend\Test\Fixture\Date',
                ['params' => [], 'data' => ['pattern' => $reportFilter]]
            );
            $viewsReport[$name] = $date->getData();
        }
        return $viewsReport;
    }
}
