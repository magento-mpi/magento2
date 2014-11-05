<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Rma\Test\Fixture\ReturnItem;

class RmaTest extends Functional
{
    /**
     * Returning items using return merchandise authorization.
     *
     * @ZephirId MAGETWO-12432
     */
    public function testRma()
    {
        // Setup Preconditions:
        $this->configureRma();

        $payPalExpressOrder = $this->guestCheckoutPayPal();
        $products = $payPalExpressOrder->getProducts();
        $this->closeSalesOrder($payPalExpressOrder);

        $orderId = $payPalExpressOrder->getOrderId();

        // Step 1:  Go to frontend
        $homePage = Factory::getPageFactory()->getCmsIndexIndex();
        $homePage->open();

        // Step 2:  Click on "Orders and Returns" link in the footer
        $homePage->getFooterBlock()->clickLink('Orders and Returns');

        // Step 3: Fill "Order and Returns" form with Test Data from the Pre-Conditions
        $returnItem = Factory::getFixtureFactory()->getMagentoRmaReturnItem();
        $returnItem->prepareData($payPalExpressOrder);
        $searchForm = Factory::getPageFactory()->getSalesGuestForm()->getSearchForm();
        $orderInfo = [
            'order_id' => $returnItem->getData('order_id'),
            'billing_last_name' => $returnItem->getData('billing_last_name'),
            'find_order_by' => $returnItem->getData('find_order_by'),
            'email_address' => $returnItem->getData('email_address'),
        ];
        $searchForm->fillForm($orderInfo);



        // Step 4: Click "Continue"
        $searchForm->submit();

        // Step 5: Click "Return" link
        $viewBlock = Factory::getPageFactory()->getSalesGuestView()->getViewBlock();
        $viewBlock->clickLink('Return');

        // Steps 6 - 9:
        $returnId = $this->createRma($returnItem, $payPalExpressOrder);

        // Step 10: Login to Backend as Admin
        Factory::getApp()->magentoBackendLoginUser();

        // Step 11: Sales->Order->Returns
        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(['id' => $orderId]);
        $orderPage->getFormTabsBlock()->openTab('returns');

        // Step 12: Open the Returns page, navigate to Return Items tab
        $orderPage->getOrderReturnsBlock()->searchAndOpen(['id' => $returnId]);
        $rmaPage = Factory::getPageFactory()->getAdminRmaView();
        $rmaPage->getRmaForm()->openTab('items');
        $tabItemsData = $rmaPage->getRmaForm()->getTabElement('items')->getDataFormTab()['items'];
        $this->assertTrue(
            $this->verifyRmaItems($products, $returnItem, $tabItemsData),
            'Product lists does not match items returned list'
        );

        // Step 13: Authorize Simple and Configurable Product
        $itemsData = [];
        foreach ($returnItem->getProductNames() as $productName) {
            $itemsData[] = [
                'product' => $productName,
                'qty_authorized' => $returnItem->getQuantity(),
                'status' => 'Authorized'
            ];
        }
        $rmaPage->getRmaForm()->getTabElement('items')->fillFormTab(['items' => ['value' => $itemsData]]);
        $rmaPage->getPageActions()->saveAndContinue();
        $rmaPage->getMessagesBlock()->waitSuccessMessage();

        // Step 14: Process Return for Simple and Configurable Product
        $itemsData = [];
        foreach ($returnItem->getProductNames() as $productName) {
            $itemsData[] = [
                'product' => $productName,
                'qty_authorized' => $returnItem->getQuantity(),
                'status' => 'Return Received'
            ];
        }
        $rmaPage->getRmaForm()->getTabElement('items')->fillFormTab(['items' => ['value' => $itemsData]]);
        $rmaPage->getPageActions()->saveAndContinue();
        $rmaPage->getMessagesBlock()->waitSuccessMessage();

        // Step 15: Approve Return for Simple and Configurable Product
        $itemsData = [];
        foreach ($returnItem->getProductNames() as $productName) {
            $itemsData[] = [
                'product' => $productName,
                'qty_authorized' => $returnItem->getQuantity(),
                'status' => 'Approved'
            ];
        }
        $rmaPage->getRmaForm()->getTabElement('items')->fillFormTab(['items' => ['value' => $itemsData]]);
        $rmaPage->getPageActions()->saveAndContinue();
        $rmaPage->getMessagesBlock()->waitSuccessMessage();
    }

    /**
     * Sets Rma configuration on application backend.
     *
     * @return void
     */
    private function configureRma()
    {
        // precondition 1: Configure RMA Settings
        $enableRma = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $enableRma->switchData('enable_rma');
        $enableRma->persist();
    }

    /**
     * Completes guest checkout using PayPalExpress.
     *
     * @return \Magento\Sales\Test\Fixture\PaypalExpressOrder
     */
    private function guestCheckoutPayPal()
    {
        // precondition 2a: MAGETWO-12415 - Guest Checkout using "Checkout with PayPal":
        $payPalExpressOrder = Factory::getFixtureFactory()->getMagentoSalesPaypalExpressOrder();
        $configurable = Factory::getFixtureFactory()->getMagentoConfigurableProductConfigurableProduct();
        $configurable->persist();
        $payPalExpressOrder->setAdditionalProducts([$configurable]);
        $payPalExpressOrder->persist();
        return $payPalExpressOrder;
    }

    /**
     * Closes the sales order on the application backend.
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
     * @param $returnItem
     * @param $payPalExpressOrder
     * @return int $returnId
     */
    private function createRma($returnItem, $payPalExpressOrder)
    {
        // Step 6: Fill "Return Items Information" form (simple product)
        $returnItemForm = Factory::getPageFactory()->getRmaGuestCreate()->getReturnItemForm();
        $returnItem->switchData('rma');

        $returnItem->addProductName($payPalExpressOrder->getProduct(0)->getName());
        $returnItemForm->fillRma('0', $returnItem);

        // Step 7: Click "Add Item to Return" for the configurable product
        $returnItemForm->submitAddItemToReturn();

        // Step 8: Fill "Return Items Information" form (configurable product)
        $returnItem->addProductName($payPalExpressOrder->getProduct(1)->getName());
        $returnItemForm->fillRma('1', $returnItem);

        // Step 9: Submit the return.
        $returnItemForm->submitReturn();

        // Validate that the success message is displayed on the 'returns' page
        $completedReturn = Factory::getPageFactory()->getSalesGuestReturns();
        $completedReturn->getMessagesBlock()->waitSuccessMessage();

        // Get the return id in order to validate on the grid.
        $successMessage = $completedReturn->getMessagesBlock()->getSuccessMessages();
        $returnId = [];
        preg_match('/#(.*?)\./s', $successMessage, $returnId);
        $returnId = $returnId[1];

        // Validate that the returns grid is now displayed and contains the return just submitted
        $returnsBlock = $completedReturn->getReturnsReturnsBlock();
        $this->assertTrue(
            $returnsBlock->getRmaRowById($returnId)->isVisible(),
            "Return Id was not found on the returns grid."
        );

        return $returnId;
    }

    /**
     * Verify rma items on backend.
     *
     * @param array $products
     * @param ReturnItem $returnItem
     * @param array $tabItemsData
     * @throws \Exception
     * @return bool
     */
    protected function verifyRmaItems(array $products, ReturnItem $returnItem, array $tabItemsData)
    {
        $fixtureItemFields = $returnItem->getData('fields');
        foreach ($products as $key => $product) {
            $tabItemData = $tabItemsData[$key];
            $productName = $product->getName();

            if (strpos($tabItemData['product'], $productName) === false) {
                return false;
            }

            foreach ($fixtureItemFields as $fieldName => $value) {
                if (!isset($tabItemData[$fieldName])) {
                    throw new \Exception('Product not found: ' . $productName);
                }
                if ($tabItemData[$fieldName] !== $value) {
                    return false;
                }
            }
        }

        return true;
    }
}
