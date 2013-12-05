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
        $searchForm = Factory::getPageFactory()->getSalesGuestForm()->getSearchForm();
        $searchForm->fillCustom($payPalExpressOrder, 'Email Address');

        // Step 4: Click "Continue"
        $searchForm->submit();

        // Step 5: Click "Return" link
        $viewBlock = Factory::getPageFactory()->getSalesGuestView()->getViewBlock();
        $viewBlock->clickLink('Return');

        // Step 6: Fill "Return Items Information" form (simple product)
        $returnItem = Factory::getFixtureFactory()->getMagentoRmaReturnItem();
        $returnItem->switchData('default');

        $returnItemForm = Factory::getPageFactory()->getRmaGuestCreate()->getReturnItemForm();
        $returnItemForm->fillCustom('0', $payPalExpressOrder->getProduct(0)->getProductName(), $returnItem);

        // Step 7: Click "Add Item to Return" for the configurable product.
        $returnItemForm->submitAddItemToReturn();

        // Step 8: Fill "Return Items Information" form (configurable product)
        $returnItemForm->fillCustom('1', $payPalExpressOrder->getProduct(1)->getProductName(), $returnItem);

        // Step 9: Submit the return.
        $returnItemForm->submitReturn();

        // Validate that the success message is displayed on the 'returns' page.
        $completedReturn = Factory::getPageFactory()->getSalesGuestReturns();
        $completedReturn->getMessageBlock()->assertSuccessMessage();

        // Get the return id in order to validate on the grid.
        $successMessage = $completedReturn->getMessageBlock()->getSuccessMessages();
        $startPosition = strpos($successMessage, '#') + 1;
        $endPosition = strpos($successMessage, '.');
        $returnId = substr($successMessage, $startPosition, $endPosition - $startPosition);

        // Validate that the returns grid is now displayed and contains the return just submitted.
        $returnsBlock = $completedReturn->getMyReturnsBlock();
        $returnsBlock->assertReturn($returnId);

        // Step 10: Login to Backend as Admin
        Factory::getApp()->magentoBackendLoginUser();

        // Step 11: Sales->Order->Returns
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
        //$orderPage->getOrderViewTabsBlock()->clickReturnsLink();
    }
}
