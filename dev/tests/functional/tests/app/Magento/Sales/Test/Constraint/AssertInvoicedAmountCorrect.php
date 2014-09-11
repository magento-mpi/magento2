<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Magento\Sales\Test\Page\OrderView;
use Magento\Sales\Test\Page\InvoicesView;
use Magento\Sales\Test\Page\OrderHistory;
use Magento\Sales\Test\Fixture\OrderInjectable;

/**
 * Class AssertInvoicedAmountCorrect
 * Assert that invoiced Grand Total amount is equal to placed order Grand total amount on invoice page (frontend)
 */
class AssertInvoicedAmountCorrect extends AbstractAssertOnFrontend
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that invoiced Grand Total amount is equal to placed order Grand total amount on invoice page (frontend)
     *
     * @param OrderHistory $orderHistory
     * @param OrderInjectable $order
     * @param OrderView $orderView
     * @param InvoicesView $invoicesView
     * @param int $invoiceId
     * @return void
     */
    public function processAssert(
        OrderHistory $orderHistory,
        OrderInjectable $order,
        OrderView $orderView,
        InvoicesView $invoicesView,
        $invoiceId
    ) {
        $this->loginCustomerAndOpenOrderPage($order->getDataFieldConfig('customer_id')['source']->getCustomer());
        $orderHistory->getOrderHistoryBlock()->openOrderById($order->getId());
        $orderView->getOrderViewBlock()->openLinkByName('Invoices');

        \PHPUnit_Framework_Assert::assertEquals(
            number_format($order->getPrice()['grand_invoice_total'], 2),
            $invoicesView->getInvoiceBlock()->getItemInvoiceBlock($invoiceId)->getGrandTotal()
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Invoiced Grand Total amount is equal to placed order Grand Total amount on invoice page.';
    }
}
