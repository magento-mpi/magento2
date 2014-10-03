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
     * @param bool $total
     * @return array
     */
    public function getSalesResults($total = false)
    {
        $orders = [];
        $filterRows = $total ? $this->totalRows : $this->filterRows;
        $row = $this->_rootElement->find(sprintf($filterRows, $this->rows[0]), Locator::SELECTOR_XPATH);
        if (!$row->isVisible()) {
            return array_fill_keys($this->rows, 0);
        }
        foreach ($this->rows as $row) {
            $value = $this->_rootElement->find(sprintf($this->filterRows, $row), Locator::SELECTOR_XPATH)->getText();
            $orders[$row] = preg_replace('`[$,]`', '', $value);
        }

        return $orders;
    }
}
