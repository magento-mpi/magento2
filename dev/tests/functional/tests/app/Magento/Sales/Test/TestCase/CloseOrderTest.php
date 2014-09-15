<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Sales\Test\Fixture\OrderCheckout;
use Magento\Sales\Test\Fixture\PaypalStandardOrder;

/**
 * Class CloseOrderTest
 *
 */
class CloseOrderTest extends Functional
{
    /**
     * Test the closing of sales order for various payment methods.
     *
     * @param OrderCheckout $fixture
     * @dataProvider dataProviderOrder
     *
     * @ZephyrId MAGETWO-12434, MAGETWO-12833, MAGETWO-13014, MAGETWO-13015, MAGETWO-13019, MAGETWO-13020, MAGETWO-13018
     */
    public function testCloseOrder(OrderCheckout $fixture)
    {
        $fixture->persist();

        //Data
        $orderId = $fixture->getOrderId();
        $grandTotal = $fixture->getGrandTotal();

        //Pages
        $pageFactory = Factory::getPageFactory();
        $orderPage = $pageFactory->getSalesOrder();
        $newInvoicePage = $pageFactory->getSalesOrderInvoiceNew();
        $newShipmentPage = $pageFactory->getSalesOrderShipmentNew();

        //Steps
        Factory::getApp()->magentoBackendLoginUser();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(['id' => $orderId]);
        $this->assertContains(
            $grandTotal,
            Factory::getPageFactory()->getSalesOrderView()->getOrderTotalsBlock()->getGrandTotal(),
            'Incorrect grand total value for the order #' . $orderId
        );

        /** @var \Magento\Sales\Test\Block\Adminhtml\Order\History $orderHistoryBlock */
        $orderHistoryBlock = Factory::getPageFactory()->getSalesOrderView()->getOrderHistoryBlock();

        if (!($fixture instanceof PaypalStandardOrder)) {
            $orderPage->getOrderActionsBlock()->invoice();
            $newInvoicePage->getInvoiceTotalsBlock()->setCaptureOption('Capture Online');
            $newInvoicePage->getInvoiceTotalsBlock()->submit();
            $this->assertContains(
                $orderPage->getMessagesBlock()->getSuccessMessages(),
                'The invoice has been created.',
                'No success message on invoice creation'
            );

            $this->assertContains(
                $grandTotal,
                $orderHistoryBlock->getCommentsHistory(),
                'Incorrect captured amount value for the order #' . $orderId
            );
        } else {
            $this->assertContains(
                $grandTotal,
                $orderHistoryBlock->getCapturedAmount(),
                'Incorrect captured amount value for the order #' . $orderId
            );
        }

        $orderPage->getOrderActionsBlock()->ship();
        $newShipmentPage->getTotalsBlock()->submit();
        $this->assertContains(
            $orderPage->getMessagesBlock()->getSuccessMessages(),
            'The shipment has been created.',
            'No success message on shipment creation'
        );
        $tabsWidget = $orderPage->getFormTabsBlock();

        //Verification on invoice tab
        $tabsWidget->openTab('invoices');
        $this->assertContains(
            $orderPage->getInvoicesGrid()->getInvoiceAmount(),
            $grandTotal
        );

        //Verification on transaction tab
        $tabsWidget->openTab('transactions');
        $this->assertContains(
            $orderPage->getTransactionsGrid()->getTransactionType(),
            'Capture'
        );
        //Verification on order grid
        $orderPage->open();
        $this->assertTrue(
            $orderPage->getOrderGridBlock()->isRowVisible(['id' => $orderId, 'status' => 'Complete']),
            "Order # $orderId in complete state was not found on the grid!"
        );
    }

    /**
     * Data providers for creating an order
     *
     * @return array
     */
    public function dataProviderOrder()
    {
        return [
            [Factory::getFixtureFactory()->getMagentoSalesPaypalExpressOrder()],
            [Factory::getFixtureFactory()->getMagentoSalesAuthorizeNetOrder()],
            [Factory::getFixtureFactory()->getMagentoSalesPaypalPaymentsProOrder()],
            [Factory::getFixtureFactory()->getMagentoSalesPaypalPaymentsAdvancedOrder()],
            [Factory::getFixtureFactory()->getMagentoSalesPaypalPayflowProOrder()],
            [Factory::getFixtureFactory()->getMagentoSalesPaypalStandardOrder()],
            [Factory::getFixtureFactory()->getMagentoSalesPaypalPayflowLinkOrder()]
        ];
    }
}
