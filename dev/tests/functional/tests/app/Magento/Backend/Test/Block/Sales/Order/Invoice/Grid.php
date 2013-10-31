<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\Sales\Order\Invoice;

use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 * Sales order grid
 *
 * @package Magento\Backend\Test\Block\Sales\Order\Invoice
 */
class Grid extends GridInterface
{
    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        $this->filters = array(
            'id' => array(
                'selector' => '#order_invoices_filter_increment_id'
            )
        );
    }

    /**
     * Get first invoice amount
     *
     * @return array|string
     */
    public function getInvoiceAmount()
    {
        return $this->_rootElement->find('td.col-qty.col-base_grand_total')->getText();
    }
}
