<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

use Mtf\Factory\Factory;
use Magento\Sales\Test\Fixture\OrderCheckout;

/**
 * Class OnlineRefundTest
 */
class OnlineRefundTest extends RefundTest
{
    /**
     * Tests providing refunds.
     *
     * @param OrderCheckout $fixture
     *
     * @return void
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
        $orderPage->getOrderGridBlock()->searchAndOpen(['id' => $orderId]);

        $tabsWidget = $orderPage->getFormTabsBlock();

        // Step 2: Open Invoice
        $tabsWidget->openTab('invoices');
        $orderPage->getInvoicesGrid()->clickInvoiceAmount();

        // Step 3: Click "Credit Memo" button on the Invoice Page
        $orderPage->getOrderActionsBlock()->orderInvoiceCreditMemo();

        // Step 4: Submit Credit Memo
        $creditMemoCreateBlock = Factory::getPageFactory()->getSalesOrderCreditmemoNew()->getCreateBlock();
        $creditMemoCreateBlock->refund();

        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $tabsWidget = $orderPage->getFormTabsBlock();

        $this->assertContains(
            'You created the credit memo.',
            $orderPage->getMessagesBlock()->getSuccessMessages()
        );

        // Step 5: Go to "Credit Memos" tab
        $tabsWidget->openTab('creditmemos');
        $this->assertContains(
            $fixture->getGrandTotal(),
            $orderPage->getCreditMemosGrid()->getRefundAmount(),
            'Incorrect refund amount for the order #' . $orderId
        );
        $this->assertContains(
            $orderPage->getCreditMemosGrid()->getStatus(),
            'Refunded'
        );

        // Step 6: Go to Transactions tab
        $tabsWidget->openTab('transactions');
        $this->assertContains(
            $orderPage->getTransactionsGrid()->getTransactionType(),
            'Refund'
        );
    }

    /**
     * Data providers for creating, closing and refunding an order
     *
     * @return array
     */
    public function dataProviderOrder()
    {
        return [
            [Factory::getFixtureFactory()->getMagentoSalesPaypalExpressOrder()],
            [Factory::getFixtureFactory()->getMagentoSalesPaypalPayflowProOrder()],
            [Factory::getFixtureFactory()->getMagentoSalesPaypalPaymentsProOrder()],
            [Factory::getFixtureFactory()->getMagentoSalesPaypalPaymentsAdvancedOrder()],
            [Factory::getFixtureFactory()->getMagentoSalesPaypalPayflowLinkOrder()]
        ];
    }
}
