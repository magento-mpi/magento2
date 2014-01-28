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

namespace Magento\Sales\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Sales\Test\Fixture\OrderCheckout;

class OnlineRefundTest extends RefundTest
{
    /**
     * Tests providing refunds.
     *
     * @dataProvider dataProviderOrder
     * @ZephirId MAGETWO-12436, MAGETWO-13061, MAGETWO-13062, MAGETWO-13063, MAGETWO-13059
     */
    public function testRefund(OrderCheckout $fixture)
    {
        // Setup preconditions
        parent::setupPreconditions($fixture);

        $orderId = $fixture->getOrderId();

        // Step 1: Order View Page
        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));

        $tabsWidget = $orderPage->getFormTabsBlock();

        // Step 2: Open Invoice
        $tabsWidget->openTab('sales_order_view_tabs_order_invoices');
        $orderPage->getInvoicesGrid()->clickInvoiceAmount();

        // Step 3: Click "Credit Memo" button on the Invoice Page
        $orderPage->getOrderActionsBlock()->orderInvoiceCreditMemo();

        // Step 4: Submit Credit Memo
        $creditMemoPage = Factory::getPageFactory()->getSalesOrderCreditMemoNew();
        $creditMemoPage->getActionsBlock()->refund();

        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $tabsWidget = $orderPage->getFormTabsBlock();

        $this->assertContains('You created the credit memo.',
            $orderPage->getMessagesBlock()->getSuccessMessages());

        // Step 5: Go to "Credit Memos" tab
        $tabsWidget->openTab('sales_order_view_tabs_order_creditmemos');
        $this->assertContains(
            $fixture->getGrandTotal(),
            $orderPage->getCreditMemosGrid()->getRefundAmount(),
            'Incorrect refund amount for the order #' . $orderId);
        $this->assertContains(
            $orderPage->getCreditMemosGrid()->getStatus(),
            'Refunded');

        // Step 6: Go to Transactions tab
        $tabsWidget->openTab('sales_order_view_tabs_order_transactions');
        $this->assertContains(
            $orderPage->getTransactionsGrid()->getTransactionType(),
            'Refund');
    }

    /**
     * Data providers for creating, closing and refunding an order
     *
     * @return array
     */
    public function dataProviderOrder()
    {
        return array(
            array(Factory::getFixtureFactory()->getMagentoSalesPaypalExpressOrder()),
            array(Factory::getFixtureFactory()->getMagentoSalesPaypalPayflowProOrder()),
            array(Factory::getFixtureFactory()->getMagentoSalesPaypalPaymentsProOrder()),
            array(Factory::getFixtureFactory()->getMagentoSalesPaypalPaymentsAdvancedOrder()),
            array(Factory::getFixtureFactory()->getMagentoSalesPaypalPayflowLinkOrder())
        );
    }
}
