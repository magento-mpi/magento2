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
use Magento\Sales\Test\Fixture\AuthorizeNetOrder;

class OfflineRefundTest extends RefundTest
{
    /**
     * Tests providing refunds.
     *
     * @dataProvider dataProviderOrder
     * @ZephirId MAGETWO-13058, MAGETWO-19985
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
        /** @var \Magento\Sales\Test\Block\Adminhtml\Order\Actions $creditMemoActionsBlock */
        $creditMemoActionsBlock = Factory::getPageFactory()->getSalesOrderCreditMemoNew()->getActionsBlock();

        if ($fixture instanceof AuthorizeNetOrder) {
            // Step 2: Open Invoice
            $tabsWidget->openTab('sales_order_view_tabs_order_invoices');
            $orderPage->getInvoicesGrid()->clickInvoiceAmount();

            // Step 3: Click "Credit Memo" button on the Invoice Page
            $orderPage->getOrderActionsBlock()->orderInvoiceCreditMemo();
        } else {
            // Step 2: Click "Credit Memo" button on the Order Page
            $orderPage->getOrderActionsBlock()->orderCreditMemo();
        }

        // Step 3/4: Submit Credit Memo
        $creditMemoActionsBlock->refundOffline();

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
    }

    /**
     * Data providers for creating, closing and refunding an order
     *
     * @return array
     */
    public function dataProviderOrder()
    {
        return array(
            array(Factory::getFixtureFactory()->getMagentoSalesAuthorizeNetOrder()),
            array(Factory::getFixtureFactory()->getMagentoSalesPaypalStandardOrder())
        );
    }
}
