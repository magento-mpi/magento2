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
use Magento\Sales\Test\Page\Adminhtml\InvoiceIndex;

/**
 * Class AssertInvoiceInInvoicesGrid
 * Assert that invoice with corresponding order ID is present in the invoices grid with corresponding amount
 */
class AssertInvoiceInInvoicesGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert invoice with corresponding order ID is present in the invoices grid with corresponding amount
     *
     * @param InvoiceIndex $invoiceIndex
     * @param OrderInjectable $order
     * @param array $ids
     * @return void
     */
    public function processAssert(InvoiceIndex $invoiceIndex, OrderInjectable $order, array $ids)
    {
        $invoiceIndex->open();
        $amount = $order->getPrice();
        $orderId = $order->getId();
        foreach ($ids['invoiceIds'] as $key => $invoiceId) {
            $filter = [
                'id' => $invoiceId,
                'order_id' => $orderId,
                'grand_total_from' => $amount[$key]['grand_invoice_total'],
                'grand_total_to' => $amount[$key]['grand_invoice_total']
            ];
            $invoiceIndex->getInvoicesGrid()->search($filter);
            $filter['grand_total_from'] = number_format($amount[$key]['grand_invoice_total'], 2);
            $filter['grand_total_to'] = number_format($amount[$key]['grand_invoice_total'], 2);
            \PHPUnit_Framework_Assert::assertTrue(
                $invoiceIndex->getInvoicesGrid()->isRowVisible($filter, false, false),
                'Invoice is absent in invoices grid on invoice index page.'
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
        return 'Invoice is present in the invoices grid with corresponding amount on invoice index page.';
    }
}
