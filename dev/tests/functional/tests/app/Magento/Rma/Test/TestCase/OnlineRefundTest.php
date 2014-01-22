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

class OnlineRefundTest extends Functional
{
    /**
     * Tests providing online refunds.
     *
     * @dataProvider dataProviderOrder
     * @ZephirId MAGETWO-12436
     */
    public function testOnlineRefund(OrderCheckout $fixture)
    {
        // Allow refunds.
        //$this->configureRma();

        // Create an order.
        $fixture->persist();

        // Close the order.
        // TODO:  Use curl handler instead of this UI handler version.
        Factory::getApp()->magentoSalesCloseOrder($fixture);
        $orderId = $fixture->getOrderId();

        // Step 1: Order View Page
        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));

        $tabsWidget = $orderPage->getFormTabsBlock();

        // Step 2: Open Invoice
        $tabsWidget->openTab('sales_order_view_tabs_order_invoices');
        // TODO:  Need invoice id from close order curl handler.
        //$orderPage->getInvoicesGrid()->searchAndSelect(array('id' => $invoiceId));

        // Step 3: Click "Credit Memo" button on the Invoice Page
        $orderPage->getOrderActionsBlock()->creditMemo();

        // Step 4: Submit Credit Memo
        $creditMemoPage = Factory::getPageFactory()->getSalesOrderCreditMemoNew();
        //$creditMemoPage->getActionsBlock()->refund();
        $this->assertContains('You created the credit memo',
            $orderPage->getMessagesBlock()->getSuccessMessages());

        // Step 5: Go to "Credit Memos" tab
        $tabsWidget->openTab('sales_order_view_tabs_order_creditmemos');
        // TODO:  Display invoice id, not order id
        $this->assertContains(
            $fixture->getGrandTotal(),
            $orderPage->getCreditMemosGrid()->getRefundAmount(),
            'Incorrect refund total value for the invoice #' . $orderId);

        // Step 6: Go to Transactions tab
        $tabsWidget->openTab('sales_order_view_tabs_order_transactions');
        $this->assertContains(
            $orderPage->getTransactionsGrid()->getTransactionType(),
            'Refund'
        );
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
            //array(Factory::getFixtureFactory()->getMagentoSalesAuthorizeNetOrder()),
            //array(Factory::getFixtureFactory()->getMagentoSalesPaypalPaymentsProOrder()),
            //array(Factory::getFixtureFactory()->getMagentoSalesPaypalPaymentsAdvancedOrder()),
            //array(Factory::getFixtureFactory()->getMagentoSalesPaypalPayflowProOrder()),
            //array(Factory::getFixtureFactory()->getMagentoSalesPaypalStandardOrder()),
            //array(Factory::getFixtureFactory()->getMagentoSalesPaypalPayflowLinkOrder())
        );
    }
}
