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
     * Attributes of the return
     *
     * @var \Magento\Rma\Test\Fixture\ReturnItem
     */
    protected $returnItem;

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
        $searchOrder = Factory::getFixtureFactory()->getMagentoRmaOrderSearch();
        $searchOrder->setOrderId($orderId);
        $searchOrder->setFindOrderBy('Email Address');
        $searchOrder->setBillingLastname($payPalExpressOrder->getBillingAddress()->getData('fields/lastname/value'));
        $searchOrder->setEmailAddress($payPalExpressOrder->getCustomer()->getData('fields/login_email/value'));

        $searchForm = Factory::getPageFactory()->getSalesGuestForm()->getSearchForm();
        $searchForm->fillCustom($searchOrder);

        // Step 4: Click "Continue"
        $searchForm->submit();

        // Step 5: Click "Return" link
        $viewBlock = Factory::getPageFactory()->getSalesGuestView()->getViewBlock();
        $viewBlock->clickLink('Return');

        // Steps 6 - 9:
        $returnId = $this->createRma($payPalExpressOrder);

        // Step 10: Login to Backend as Admin
        Factory::getApp()->magentoBackendLoginUser();

        // Step 11: Sales->Order->Returns
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
        $orderPage->getFormTabsBlock()->openTab('sales_order_view_tabs_order_rma');

        // Step 12: Open the Returns page, navigate to Return Items tab
        $orderPage->getOrderReturnsBlock()->searchAndOpen(array('id' => $returnId));
        $rmaPage = Factory::getPageFactory()->getAdminRmaEdit();
        $rmaPage->getFormTabsBlock()->openTab('rma_info_tabs_items_section');
        $this->assertTrue($rmaPage->getRmaEditFormBlock()->assertProducts($products, $this->returnItem),
            'Product lists does not match items returned list'
        );

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

    /**
     * Sets Rma configuration on application backend
     */
    private function configureRma()
    {
        // precondition 1: Configure RMA Settings
        $enableRma = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $enableRma->switchData('enable_rma');
        $enableRma->persist();
    }

    /**
     * Completes guest checkout using PayPalExpress
     *
     * @return \Magento\Sales\Test\Fixture\PaypalExpressOrder
     */
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

    /**
     * Closes the sales order on the application backend
     *
     * @param $payPalExpressOrder
     * @return \Magento\Sales\Test\Handler\Ui\CloseOrder
     */
    private function closeSalesOrder($payPalExpressOrder)
    {
        // precondition 2b: MAGETWO-12434 - Closing a Sales Order paid with PayPal Express Checkout
        return Factory::getApp()->magentoSalesCloseOrder($payPalExpressOrder);
    }

    /**
     * Creates a Rma on the frontend.
     *
     * @param $payPalExpressOrder
     * @return int $returnId
     */
    private function createRma($payPalExpressOrder) {
        // Step 6: Fill "Return Items Information" form (simple product)
        $returnItem = Factory::getFixtureFactory()->getMagentoRmaReturnItem();
        $returnItem->switchData('default');
        $this->returnItem=$returnItem;

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
        $returnId = array();
        preg_match('/#(.*?)\./s', $successMessage, $returnId);
        $returnId = $returnId[1];

        // Validate that the returns grid is now displayed and contains the return just submitted.
        $returnsBlock = $completedReturn->getReturnsReturnsBlock();
        $this->assertTrue(
            $returnsBlock->isRowVisible($returnId),
            "Return Id was not found on the returns grid."
        );

        return $returnId;
    }
}
