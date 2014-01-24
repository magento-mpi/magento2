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

class OnlineRefundTest extends Functional
{
    /**
     * Tests providing online refunds.
     *
     * @dataProvider dataProviderOrder
     * @ZephirId MAGETWO-12436, MAGETWO-18766, MAGETWO-18774, MAGETWO-18775, MAGETWO-18777, MAGETWO-18778, MAGETWO-19986
     */
    public function testOnlineRefund(OrderCheckout $fixture)
    {
        // Allow refunds.
        $this->configureRma();

        // Create an order.
        $fixture->persist();

        Factory::getApp()->magentoBackendLoginUser();

        // Close the order.
        Factory::getApp()->magentoSalesCloseOrder($fixture);
        $orderId = $fixture->getOrderId();

        // Step 1: Order View Page
        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));

        $tabsWidget = $orderPage->getFormTabsBlock();

        if (!($fixture instanceof PaypalStandardOrder)) {
            // Step 2: Open Invoice
            $tabsWidget->openTab('sales_order_view_tabs_order_invoices');
            // TODO:  Need invoice id from close order curl handler.
            $orderPage->getInvoicesGrid()->clickInvoiceAmount();
            //$orderPage->getInvoicesGrid()->searchAndSelect(array('id' => $invoiceId));

            // Step 3: Click "Credit Memo" button on the Invoice Page
            $orderPage->getOrderActionsBlock()->orderInvoiceCreditMemo();

            // Step 4: Submit Credit Memo
            $creditMemoPage = Factory::getPageFactory()->getSalesOrderCreditMemoNew();
            $creditMemoPage->getActionsBlock()->refund();
        }
        else {
            // Step 2: Click "Credit Memo" button on the Order Page
            $tabsWidget->openTab('sales_order_view_tabs_order_creditmemos');
            $orderPage->getOrderActionsBlock()->creditMemo();

            // Step 3: Submit Credit Memo
            $creditMemoPage = Factory::getPageFactory()->getSalesOrderCreditMemoNew();
            $creditMemoPage->getActionsBlock()->refundOffline();
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

        if (!($fixture instanceof PaypalStandardOrder)) {
            // Step 6: Go to Transactions tab
            $tabsWidget->openTab('sales_order_view_tabs_order_transactions');
            $this->assertContains(
                $orderPage->getTransactionsGrid()->getTransactionType(),
                'Refund');
        }
    }

    /**
     * Sets Rma configuration on application backend
     */
    private function configureRma()
    {
        $enableRma = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $enableRma->switchData('enable_rma');
        $enableRma->persist();
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
            (Factory::getFixtureFactory()->getMagentoSalesPaypalPaymentsProOrder()),
            array(Factory::getFixtureFactory()->getMagentoSalesAuthorizeNetOrder()),
            array(Factory::getFixtureFactory()->getMagentoSalesPaypalStandardOrder()),
            array(Factory::getFixtureFactory()->getMagentoSalesPaypalPaymentsAdvancedOrder()),
            array(Factory::getFixtureFactory()->getMagentoSalesPaypalPayflowLinkOrder())
        );
    }
}
