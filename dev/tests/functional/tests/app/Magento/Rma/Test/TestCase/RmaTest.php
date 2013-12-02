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
use Magento\Catalog\Test\Fixture\AbstractProduct;

Class RmaTest extends Functional
{
    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     *
     * Returning items using return merchandise authorization
     *
     * @ZephirId MAGETWO-12432
     */
    public function testRma()
    {
        // precondition 1: Configure RMA Settings
        $enableRma = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $enableRma->switchData('enable_rma');
        $enableRma->persist();

        // precondition 2a: MAGETWO-12415 - Guest Checkout using "Checkout with PayPal":
        $payPalExpressOrder = Factory::getFixtureFactory()->getMagentoSalesPaypalExpressOrder();
        $payPalExpressOrder->persist();

        // precondition 2b: MAGETWO-12434 - Closing a Sales Order paid with PayPal Express Checkout
        $orderId = $payPalExpressOrder->getOrderId();
        //Pages
        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $newInvoicePage = Factory::getPageFactory()->getSalesOrderInvoiceNew();
        $newShipmentPage = Factory::getPageFactory()->getSalesOrderShipmentNew();

        Factory::getApp()->magentoBackendLoginUser();

        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));

        //Create the Shipment
        $orderPage->getOrderActionsBlock()->clickShipButton();
        $newShipmentPage->getTotalsBlock()->submit();

        //Create the Invoice
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
        $orderPage->getOrderActionsBlock()->clickInvoiceButton();
        $newInvoicePage->getInvoiceTotalsBlock()->submit();

        // Step 1:  Go to frontend
        $homePage = Factory::getPageFactory()->getCmsIndexIndex();
        $homePage->open();

        // Step 2:  Click on "Orders and Returns" link in the footer
        $homePage->getFooterBlock()->clickLink('Orders and Returns');

        // Step 3: Fill "Order and Returns" form with Test Data from the Pre-Conditions
        $ordersAndReturnsPage = Factory::getPageFactory()->getSalesGuestForm();
        $ordersAndReturnsForm = $ordersAndReturnsPage->getOrdersAndReturnsSearchForm();
        $ordersAndReturnsForm->fillCustom($payPalExpressOrder, 'email');

        // Step 4: Click "Continue"
        $ordersAndReturnsForm->submit();

        // Step 5: Click "Return" link
        $ordersAndReturnsViewPage = Factory::getPageFactory()->getSalesGuestView();
        //$ordersAndReturnsViewPage->getOrdersAndReturnsViewBlock()->clickLink('Return');
    }
}