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

namespace Magento\Sales\Test\Block\Adminhtml\Order\Invoice;

use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 * Sales order grid
 *
 * @package Magento\Sales\Test\Block\Adminhtml\Order\Invoice
 */
class Grid extends GridInterface
{
    /**
     * {@inheritdoc}
     */
    protected $filters = array(
        'id' => array(
            'selector' => '#order_invoices_filter_increment_id'
        )
    );

    /**
     * Invoice amount
     *
     * @var string
     */
    protected $invoiceAmount = 'td.col-qty.col-base_grand_total';

    /**
     * An element locator which allows to select entities in grid
     *
     * @var string
     */
    protected $selectItem = 'tbody tr .col-invoice-number';

    /**
     * Get first invoice amount
     *
     * @return array|string
     */
    public function getInvoiceAmount()
    {
        return $this->_rootElement->find($this->invoiceAmount)->getText();
    }

    /**
     * Click the first invoice amount
     *
     * @return void
     */
    public function clickInvoiceAmount()
    {
        $this->_rootElement->find($this->invoiceAmount)->click();
    }
}
