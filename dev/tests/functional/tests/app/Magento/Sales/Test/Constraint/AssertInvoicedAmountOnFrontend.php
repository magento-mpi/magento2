<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Sales\Test\Constraint;

use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\InvoiceView;
use Magento\Sales\Test\Page\OrderHistory;
use Magento\Sales\Test\Page\OrderView;

/**
 * Class AssertInvoicedAmountOnFrontend
 * Assert that invoiced Grand Total amount is equal to placed order Grand total amount on invoice page (frontend)
 */
class AssertInvoicedAmountOnFrontend extends AbstractAssertOrderOnFrontend
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that invoiced Grand Total amount is equal to placed order Grand total amount on invoice page (frontend)
     *
     * @param OrderHistory $orderHistory
     * @param OrderInjectable $order
     * @param OrderView $orderView
     * @param InvoiceView $invoiceView
     * @param array $ids
     * @return void
     */
    public function processAssert(
        OrderHistory $orderHistory,
        OrderInjectable $order,
        OrderView $orderView,
        InvoiceView $invoiceView,
        array $ids
    ) {
        $this->loginCustomerAndOpenOrderPage($order->getDataFieldConfig('customer_id')['source']->getCustomer());
        $orderHistory->getOrderHistoryBlock()->openOrderById($order->getId());
        $orderView->getOrderViewBlock()->openLinkByName('Invoices');
        foreach ($ids['invoiceIds'] as $key => $invoiceId) {
            \PHPUnit_Framework_Assert::assertEquals(
                number_format($order->getPrice()[$key]['grand_invoice_total'], 2),
                $invoiceView->getInvoiceBlock()->getItemBlock($invoiceId)->getGrandTotal()
            );
        }
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
