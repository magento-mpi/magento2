<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Magento\Sales\Test\Page\OrderView;
use Magento\Sales\Test\Page\CreditMemoView;
use Magento\Sales\Test\Page\OrderHistory;
use Magento\Sales\Test\Fixture\OrderInjectable;

/**
 * Class AssertRefundedGrandTotal
 * Assert that refunded grand total is equal to data from fixture on My Account page
 */
class AssertRefundedGrandTotal extends AbstractAssertOrderOnFrontend
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that refunded grand total is equal to data from fixture on My Account page
     *
     * @param OrderHistory $orderHistory
     * @param OrderInjectable $order
     * @param OrderView $orderView
     * @param CreditMemoView $creditMemoView
     * @param array $ids
     * @return void
     */
    public function processAssert(
        OrderHistory $orderHistory,
        OrderInjectable $order,
        OrderView $orderView,
        CreditMemoView $creditMemoView,
        array $ids
    ) {
        $this->loginCustomerAndOpenOrderPage($order->getDataFieldConfig('customer_id')['source']->getCustomer());
        $orderHistory->getOrderHistoryBlock()->openOrderById($order->getId());
        $orderView->getOrderViewBlock()->openLinkByName('Refunds');
        foreach ($ids['creditMemoIds'] as $key => $creditMemoId) {
            \PHPUnit_Framework_Assert::assertEquals(
                number_format($order->getPrice()[$key]['grand_creditmemo_total'], 2),
                $creditMemoView->getCreditMemoBlock()->getItemBlock($creditMemoId)->getGrandTotal()
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
        return 'Credit memo Grand Total amount is equal to placed order Grand Total amount on credit memo page.';
    }
}
