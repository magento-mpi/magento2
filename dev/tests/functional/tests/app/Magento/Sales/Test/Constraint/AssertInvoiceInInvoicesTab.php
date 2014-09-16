<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderView;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;
use Magento\Sales\Test\Block\Adminhtml\Order\View\Tab\Invoices\Grid;

/**
 * Class AssertInvoiceInInvoicesTab
 * Assert that invoice is present in the invoices tab of the order with corresponding amount(Grand Total)
 */
class AssertInvoiceInInvoicesTab extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that invoice is present in the invoices tab of the order with corresponding amount(Grand Total)
     *
     * @param OrderView $orderView
     * @param OrderIndex $orderIndex
     * @param OrderInjectable $order
     * @param string $invoiceId
     * @return void
     */
    public function processAssert(
        OrderView $orderView,
        OrderIndex $orderIndex,
        OrderInjectable $order,
        $invoiceId
    ) {
        $orderIndex->open();
        $orderIndex->getSalesOrderGrid()->searchAndOpen(['id' => $order->getId()]);
        $orderView->getOrderForm()->openTab('invoices');
        $amount = $order->getPrice()['grand_invoice_total'];
        $filter = [
            'id' => $invoiceId,
            'amount_from' => $amount,
            'amount_to' => $amount
        ];
        /** @var Grid $grid */
        $grid = $orderView->getOrderForm()->getTabElement('invoices')->getGridBlock();
        $grid->search($filter);
        $filter['amount_from'] = $filter['amount_to'] = number_format($amount, 2);
        \PHPUnit_Framework_Assert::assertTrue(
            $grid->isRowVisible($filter, false, false),
            'Invoice is absent on invoices tab.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Invoice is present on invoices tab.';
    }
}
