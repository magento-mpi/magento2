<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Block\Adminhtml\Sales\Invoiced;

use Magento\Backend\Test\Block\Widget\Grid as ParentGrid;
use Mtf\Client\Element\Locator;

/**
 * Class Grid
 * Invoice Report filter grid
 */
class Grid extends ParentGrid
{
    /**
     * Filters row locator
     *
     * @var string
     */
    protected $filterRows = '(//tr[td[contains(@class, "col-qty")]])[last()]/td[contains(@class, "col-%s")]';

    /**
     * Filters row locator
     *
     * @var string
     */
    protected $totalRows = '//tfoot/tr/th[contains(@class, "col-%s")]';

    /**
     * Rows for get invoice result
     *
     * @var array
     */
    protected $rows = [
        'qty',
        'invoiced',
        'total-invoiced'
    ];

    /**
     * Get last invoice from Invoice Report grid
     *
     * @return array
     */
    public function getLastInvoiceResult()
    {
        return $this->getResults($this->filterRows);
    }

    /**
     * Get total invoice from Invoice Report grid
     *
     * @return array
     */
    public function getInvoiceTotalResult()
    {
        return $this->getResults($this->totalRows);
    }

    /**
     * Get sales data from Invoice Report grid
     *
     * @param array $filterRows
     * @return array
     */
    protected function getResults($filterRows)
    {
        $orders = [];
        $row = $this->_rootElement->find(sprintf($filterRows, $this->rows[0]), Locator::SELECTOR_XPATH);
        if (!$row->isVisible()) {
            return array_fill_keys($this->rows, 0);
        }
        foreach ($this->rows as $row) {
            $value = $this->_rootElement->find(sprintf($filterRows, $row), Locator::SELECTOR_XPATH)->getText();
            $orders[$row] = preg_replace('`[$,]`', '', $value);
        }

        return $orders;
    }
}
