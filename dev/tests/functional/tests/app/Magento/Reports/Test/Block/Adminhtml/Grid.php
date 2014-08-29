<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Block\Adminhtml;

use Mtf\Client\Element\Locator;

/**
 * Class Grid
 * Backend customer report grid block
 */
class Grid extends \Magento\Backend\Test\Block\Widget\Grid
{
    /**
     * Mapping for fields in Account Report Grid
     *
     * @var array
     */
    protected $dataMapping = [
        'report_from' => 'datepicker',
        'report_to' => 'datepicker',
        'report_period' => 'select',
    ];

    /**
     * Total results locator
     *
     * @var string
     */
    protected $totalResults = 'tfoot .col-qty';

    /**
     * Filter locator
     *
     * @var string
     */
    protected $filter = '[name=%s]';

    /**
     * Refresh button locator
     *
     * @var string
     */
    protected $refreshButton = '[data-ui-id="adminhtml-report-grid-refresh-button"]';

    /**
     * Search accounts in report grid
     *
     * @var array $customersReport
     * @return void
     */
    public function searchAccounts(array $customersReport)
    {
        foreach ($customersReport as $name => $value) {
            $this->_rootElement
                ->find(sprintf($this->filter, $name), Locator::SELECTOR_CSS, $this->dataMapping[$name])
                ->setValue($value);
        }
        $this->_rootElement->find($this->refreshButton)->click();
    }

    /**
     * Get total Results from New Accounts Report grid
     *
     * @return string
     */
    public function getTotalResults()
    {
        return $this->_rootElement->find($this->totalResults)->getText();
    }
}
