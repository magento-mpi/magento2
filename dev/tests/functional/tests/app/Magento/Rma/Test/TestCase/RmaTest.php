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

class RmaTest extends Functional
{
    /**
     * Returning items using return merchandise authorization
     *
     * @ZephirId MAGETWO-12432
     */
    public function testRma()
    {
        // Setup Preconditions:
        $this->configureRma();

        $payPalExpressOrder = $this->guestCheckoutPayPal();
        $products = $payPalExpressOrder->getProducts();
        $orderPage = $this->closeSalesOrder($payPalExpressOrder);

        $orderId = $payPalExpressOrder->getOrderId();

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
        $this->assertTrue(
            $returnsBlock->isRowVisible($returnId),
            "Return Id was not found on the returns grid."
        );

        // Step 10: Login to Backend as Admin
        Factory::getApp()->magentoBackendLoginUser();

        // Step 11: Sales->Order->Returns
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
        $orderPage->getFormTabsBlock()->openTab('sales_order_view_tabs_order_rma');

        // Step 12: Open the Returns page, navigate to Return Items tab
        $orderPage->getOrderReturnsBlock()->searchAndOpen(array('id' => $returnId));
        $rmaPage = Factory::getPageFactory()->getRmaEdit();
        $rmaPage->getFormTabsBlock()->openTab('rma_info_tabs_items_section');

        // Step 13: Authorize Simple and Configurable Product
        $rmaPage->getRmaEditFormBlock()->fillCustom($products, 1, 'AUTHORIZE_QTY');
        $rmaPage->getRmaActionsBlock()->saveAndEdit();
        $rmaPage->getMessageBlock()->assertSuccessMessage();

        // Step 14: Process Return for Simple and Configurable Product
        $rmaPage->getFormTabsBlock()->openTab('rma_info_tabs_items_section');
        $rmaPage->getRmaEditFormBlock()->fillCustom($products,1, 'RETURN_QTY');
        $rmaPage->getRmaActionsBlock()->saveAndEdit();
        $rmaPage->getMessageBlock()->assertSuccessMessage();

        // Step 15: Approve Return for Simple and Configurable Product
        $rmaPage->getFormTabsBlock()->openTab('rma_info_tabs_items_section');
        $rmaPage->getRmaEditFormBlock()->fillCustom($products, 1, 'APPROVE_QTY');
        $rmaPage->getRmaActionsBlock()->saveAndEdit();
        $rmaPage->getMessageBlock()->assertSuccessMessage();
    }

    private function configureRma()
    {
        // precondition 1: Configure RMA Settings
        $enableRma = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $enableRma->switchData('enable_rma');
        $enableRma->persist();
    }

    private function guestCheckoutPayPal()
    {
        // precondition 2a: MAGETWO-12415 - Guest Checkout using "Checkout with PayPal":
        $payPalExpressOrder = Factory::getFixtureFactory()->getMagentoSalesPaypalExpressOrder();
        $configurable = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $configurable->persist();
        $payPalExpressOrder->setAdditionalProducts(array($configurable));
        $payPalExpressOrder->persist();
        return $payPalExpressOrder;
    }

    private function closeSalesOrder($payPalExpressOrder)
    {
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

        return $orderPage;
    }
}
