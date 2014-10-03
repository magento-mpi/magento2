<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Block\Adminhtml\Sales\Orders\Viewed;

use Magento\Backend\Test\Block\Widget\Grid;
use Mtf\Client\Element\Locator;

/**
 * Class FilterGrid
 * Sales Report filter grid
 */
class FilterGrid extends Grid
{
    /**
     * Filters row locator
     *
     * @var string
     */
    protected $filterRows = '(//tr[td[contains(@class, "col-orders")]])[last()]/td[contains(@class, "col-%s")]';

    /**
     * Filters row locator
     *
     * @var string
     */
    protected $totalRows = '//tfoot/tr/th[contains(@class, "col-%s")]';

    /**
     * Rows for get sales result
     *
     * @var array
     */
    protected $rows = [
        'orders',
        'sales-items',
        'sales-total',
        'invoiced',
    ];

    /**
     * Get sales from Sales Report grid
     *
     * @return array
     */
    public function getSalesResults()
    {
        $orders = [];
        $row = $this->_rootElement->find(sprintf($this->filterRows, $this->rows[0]), Locator::SELECTOR_XPATH);
        if (!$row->isVisible()) {
            return array_fill_keys($this->rows, 0);
        }
        foreach ($this->rows as $row) {
            $value = $this->_rootElement->find(sprintf($this->filterRows, $row), Locator::SELECTOR_XPATH)->getText();
            $orders[$row] = preg_replace('`[$,]`', '', $value);
        }

        return $orders;
    }

    /**
     * Get total sales from Sales Report grid
     *
     * @return array
     */
    public function getTotalSalesResults()
    {
        $orders = [];
        $row = $this->_rootElement->find(sprintf($this->totalRows, $this->rows[0]), Locator::SELECTOR_XPATH);
        if (!$row->isVisible()) {
            return array_fill_keys($this->rows, 0);
        }
        foreach ($this->rows as $row) {
            $value = $this->_rootElement->find(sprintf($this->totalRows, $row), Locator::SELECTOR_XPATH)->getText();
            $orders[$row] = preg_replace('`[$,]`', '', $value);
        }

        return $orders;
    }
}
