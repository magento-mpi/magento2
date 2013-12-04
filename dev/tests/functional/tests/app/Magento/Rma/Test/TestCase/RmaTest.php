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
     *
     * Returning items using return merchandise authorization
     *
     * @ZephirId MAGETWO-12432
     */
    public function testRma()
    {
        // precondition 1: Configure RMA Settings
        Factory::getApp()->magentoBackendLoginUser();
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
        $orderPage->getOrderActionsBlock()->ship();
        $newShipmentPage->getTotalsBlock()->submit();

        //Create the Invoice
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
        $orderPage->getOrderActionsBlock()->invoice();
        $newInvoicePage->getInvoiceTotalsBlock()->submit();

        // Step 1:  Go to frontend
        $homePage = Factory::getPageFactory()->getCmsIndexIndex();
        $homePage->open();

        // Step 2:  Click on "Orders and Returns" link in the footer
        $homePage->getFooterBlock()->clickLink('Orders and Returns');

        // Step 3: Fill "Order and Returns" form with Test Data from the Pre-Conditions
        $searchPage = Factory::getPageFactory()->getSalesGuestForm();
        $searchForm = $searchPage->getSearchForm();
        $searchForm->fillCustom($payPalExpressOrder, 'email');

        // Step 4: Click "Continue"
        $searchForm->submit();

        // Step 5: Click "Return" link
        $viewPage = Factory::getPageFactory()->getSalesGuestView();
        //$viewBlock = $viewPage->getViewBlock(); // TODO:  Broken.
        //$viewBlock->clickLink('Return');

        // Step 6: Fill "Return Items Information" form (simple product)
        //$returnItemPage = Factory::getPageFactory()->getRmaGuestCreate();
        //$returnItemForm = $returnItemPage->getReturnItemForm();
        //$returnItemForm->fillCustom('0');  // TODO:  Implement

        // Step 7: Click "Add Item to Return" for the configurable product.
        //$returnItemForm->submitAddItemToReturn();

        // Step 8: Fill "Return Items Information" form (configurable product)
        //$returnItemForm->fillCustom('1');  // TODO:  Implement

        // Step 9: Submit the return.
        //$returnItemForm->submitReturn();

        // Validate that the success message is displayed on the 'returns' page.
        //Factory::getPageFactory()->getSalesGuestReturns()->getMessageBlock()->assertSuccessMessage();

        // Validate that the returns grid is now displayed and contains the return just submitted.
        // TODO: Implement

        // Step 10: Login to Backend as Admin
        Factory::getApp()->magentoBackendLoginUser();

        // Step 11: Sales->Order->Returns
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
        //$orderPage->getOrderViewTabsBlock()->clickReturnsLink();
    }
}
