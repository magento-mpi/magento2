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
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class CancelOrderTest
 * Test cancel order
 *
 * @package Magento\Sales\Test\TestCase\
 */
class CancelOrderTest extends Functional
{
    /**
     * Cancel order placed by PayPal Express from product page
     *
     * @ZephyrId MAGETWO-12434
     * 
     * @dataProvider dataProviderOrder
     * @param string|int $orderId
     * @param string|int $grandTotal
     */
    public function testPayPalExpress($orderId, $grandTotal)
    {
        //Pages
        $orderPage = Factory::getPageFactory()->getAdminSalesOrder();
        $newInvoicePage = Factory::getPageFactory()->getAdminSalesOrderInvoiceNew();
        $newShipmentPage = Factory::getPageFactory()->getAdminSalesOrderShipmentNew();

        Factory::getApp()->magentoBackendLoginUser();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
        $this->assertContains(
            $grandTotal,
            Factory::getPageFactory()->getAdminSalesOrderView()->getOrderTotalsBlock()->getGrandTotal(),
            'Incorrect grand total value for the order #' . $orderId
        );

        $orderPage->getOrderActionsBlock()->invoice();
        $newInvoicePage->getInvoiceTotalsBlock()->setCaptureOption('Capture Online');
        $newInvoicePage->getInvoiceTotalsBlock()->submit();
        $this->assertContains(
            $orderPage->getMessagesBlock()->getSuccessMessages(),
            'The invoice has been created.',
            'No success message on invoice creation'
        );

        $orderPage->getOrderActionsBlock()->ship();
        $newShipmentPage->getOrderGridBlock()->submit();
        $this->assertContains(
            $orderPage->getMessagesBlock()->getSuccessMessages(),
            'The shipment has been created.',
            'No success message on shipment creation'
        );
        $tabsWidget = $orderPage->getTabsWidget();

        //Verification on invoice tab
        $tabsWidget->openTab('sales_order_view_tabs_order_invoices');
        $this->assertContains(
            $orderPage->getInvoicesGrid()->getInvoiceAmount(),
            $grandTotal
        );

        //Verification on transaction tab
        $tabsWidget->openTab('sales_order_view_tabs_order_transactions');
        $this->assertContains(
            $orderPage->getTransactionsGrid()->getTransactionType(),
            'Capture'
        );
        //Verification on order grid
        $orderPage->open();
        $this->assertTrue(
            $orderPage->getOrderGridBlock()->isRowVisible(array('id' => $orderId, 'status' => 'Complete')),
            "Order # $orderId in complete state was not found on the grid!"
        );
    }

    /**
     * Data provider for testPayPalExpress
     *
     * @return array
     */
    public function dataProviderOrder()
    {
        $paypalExpressFixture = Factory::getFixtureFactory()->getMagentoCheckoutPaypalExpress();
        return array(
                array(
                    Factory::getApp()->magentoCheckoutCreatePaypalExpressOrder($paypalExpressFixture),
                    $paypalExpressFixture->getGrandTotal()
                )
        );
    }
}
