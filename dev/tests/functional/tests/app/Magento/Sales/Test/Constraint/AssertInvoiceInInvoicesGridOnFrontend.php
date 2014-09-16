<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Magento\Sales\Test\Page\OrderHistory;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Class AssertInvoiceInInvoicesGridOnFrontend
 * Assert that invoice with corresponding order ID is present in the invoices grid with corresponding amount (frontend)
 */
class AssertInvoiceInInvoicesGridOnFrontend extends AbstractAssertOrderOnFrontend
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert invoice with corresponding order ID is present in the invoices grid with corresponding amount (frontend)
     *
     * @param OrderHistory $orderHistory
     * @param OrderInjectable $order
     * @return void
     */
    public function processAssert(OrderHistory $orderHistory, OrderInjectable $order)
    {
        $this->loginCustomerAndOpenOrderPage($order->getDataFieldConfig('customer_id')['source']->getCustomer());
        \PHPUnit_Framework_Assert::assertEquals(
            number_format($order->getPrice()['grand_order_total'], 2),
            $orderHistory->getOrderHistoryBlock()->getOrderTotalById($order->getId())
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Invoice is present in the invoices grid with corresponding amount on My Orders page.';
    }
}
