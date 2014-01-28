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

namespace Magento\Rma\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Sales\Test\Fixture\OrderCheckout;
use Magento\Sales\Test\Fixture\PaypalStandardOrder;
use Magento\Sales\Test\Fixture\AuthorizeNetOrder;

class RefundTest extends Functional
{
    /**
     * Tests providing refunds.
     *
     * @dataProvider dataProviderOrder
     * @ZephirId MAGETWO-12436, MAGETWO-13061, MAGETWO-13062, MAGETWO-13063, MAGETWO-13058, MAGETWO-13059, MAGETWO-19985
     */
    public function testRefund(OrderCheckout $fixture)
    {
        // Setup preconditions
        $this->setupPreconditions($fixture);

        $orderId = $fixture->getOrderId();

        // Step 1: Order View Page
        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));

        $tabsWidget = $orderPage->getFormTabsBlock();
        /** @var \Magento\Sales\Test\Block\Adminhtml\Order\Actions $creditMemoActionsBlock */
        $creditMemoActionsBlock = Factory::getPageFactory()->getSalesOrderCreditMemoNew().getActionsBlock();

        if (!($fixture instanceof PaypalStandardOrder)) {
            // Step 2: Open Invoice
            $tabsWidget->openTab('sales_order_view_tabs_order_invoices');
            $orderPage->getInvoicesGrid()->clickInvoiceAmount();

            // Step 3: Click "Credit Memo" button on the Invoice Page
            $orderPage->getOrderActionsBlock()->orderInvoiceCreditMemo();

            // Step 4: Submit Credit Memo
            if (!($fixture instanceof AuthorizeNetOrder)) {
                $creditMemoActionsBlock->refund();
            } else {
                $creditMemoActionsBlock->refundOffline();
            }
        }
        else {
            // Step 2: Click "Credit Memo" button on the Order Page
            $orderPage->getOrderActionsBlock()->orderCreditMemo();

            // Step 3: Submit Credit Memo
            $creditMemoActionsBlock->refundOffline();
        }

        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $tabsWidget = $orderPage->getFormTabsBlock();

        $this->assertContains('You created the credit memo.',
            $orderPage->getMessagesBlock()->getSuccessMessages());

        // Step 4/5: Go to "Credit Memos" tab
        $tabsWidget->openTab('sales_order_view_tabs_order_creditmemos');
        $this->assertContains(
            $fixture->getGrandTotal(),
            $orderPage->getCreditMemosGrid()->getRefundAmount(),
            'Incorrect refund amount for the order #' . $orderId);
        $this->assertContains(
            $orderPage->getCreditMemosGrid()->getStatus(),
            'Refunded');

        if (!($fixture instanceof PaypalStandardOrder) && !($fixture instanceof AuthorizeNetOrder)) {
            // Step 6: Go to Transactions tab
            $tabsWidget->openTab('sales_order_view_tabs_order_transactions');
            $this->assertContains(
                $orderPage->getTransactionsGrid()->getTransactionType(),
                'Refund');
        }
    }

    /**
     * Sets up the preconditions for this test.
     *
     * @param OrderCheckout $fixture
     * @return void
     */
    private function setupPreconditions(OrderCheckout $fixture)
    {
        // Enable returns
        $enableRma = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $enableRma->switchData('enable_rma');
        $enableRma->persist();

        // Create an order.
        $fixture->persist();

        // Log into the backend.
        Factory::getApp()->magentoBackendLoginUser();

        // Close the order.
        Factory::getApp()->magentoSalesCloseOrder($fixture);
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
            array(Factory::getFixtureFactory()->getMagentoSalesAuthorizeNetOrder()),
            array(Factory::getFixtureFactory()->getMagentoSalesPaypalStandardOrder()),
            array(Factory::getFixtureFactory()->getMagentoSalesPaypalPaymentsAdvancedOrder()),
            array(Factory::getFixtureFactory()->getMagentoSalesPaypalPayflowLinkOrder())
        );
    }
}
