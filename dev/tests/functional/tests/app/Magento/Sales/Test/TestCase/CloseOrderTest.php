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
use Magento\Sales\Test\Fixture\OrderCheckout;

/**
 * Class CloseOrderTest
 *
 * @package Magento\Sales\Test\TestCase\
 */
class CloseOrderTest extends Functional
{
    /**
     * Test the closing of sales order for various payment methods.
     *
     * @param OrderCheckout $fixture
     * @param null|string $paymentMethodFunction
     * @dataProvider dataProviderOrder
     *
     * @ZephyrId MAGETWO-12434, MAGETWO-12833, MAGETWO-13015, MAGETWO-13019, MAGETWO-13020
     */
    public function testCloseOrder(OrderCheckout $fixture, $paymentMethodFunction = null)
    {
        $fixture->persist();

        // Capture additional payment method data when needed
        if (!is_null($paymentMethodFunction)) {
            call_user_func_array(array($this, $paymentMethodFunction), array($fixture->getCheckoutFixture()));
        }

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
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
        $this->assertContains(
            $grandTotal,
            Factory::getPageFactory()->getSalesOrderView()->getOrderTotalsBlock()->getGrandTotal(),
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

        $this->assertContains(
            $grandTotal,
            Factory::getPageFactory()->getSalesOrderView()->getOrderHistoryBlock()->getCommentsHistory(),
            'Incorrect captured amount value for the order #' . $orderId
        );

        $orderPage->getOrderActionsBlock()->ship();
        $newShipmentPage->getTotalsBlock()->submit();
        $this->assertContains(
            $orderPage->getMessagesBlock()->getSuccessMessages(),
            'The shipment has been created.',
            'No success message on shipment creation'
        );
        $tabsWidget = $orderPage->getFormTabsBlock();

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
     * Data providers for creating an order
     *
     * @return array
     */
    public function dataProviderOrder()
    {
        return array(
            array(Factory::getFixtureFactory()->getMagentoSalesPaypalExpressOrder()),
            array(Factory::getFixtureFactory()->getMagentoSalesAuthorizeNetOrder()),
            array(Factory::getFixtureFactory()->getMagentoSalesPaypalPaymentsProOrder()),
            array(Factory::getFixtureFactory()->getMagentoSalesPaypalPaymentsAdvancedOrder(),
                  'populatePayflowAdvancedCcForm'),
            array(Factory::getFixtureFactory()->getMagentoSalesPaypalPayflowProOrder()),
            array(Factory::getFixtureFactory()->getMagentoSalesPaypalStandardOrder())
        );
    }

    /**
     * Populate additional data needed for Paypal Payments Advanced checkout.
     *
     * @param Checkout $fixture
     */
    public function populatePayflowAdvancedCcForm(Checkout $fixture) {
        /** @var \Magento\Payment\Test\Block\Form\PayflowAdvanced\Cc $formBlock */
        $formBlock = Factory::getPageFactory()->getCheckoutOnepage()->getPayflowAdvancedCcBlock();
        $formBlock->fill($fixture);
        $formBlock->pressContinue();
    }
}
