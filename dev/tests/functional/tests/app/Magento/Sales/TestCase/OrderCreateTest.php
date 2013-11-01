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

use Magento\Sales\Test\Fixture\Order;
use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Catalog\Test\Fixture\Product;

/**
 * Tests for creating order on backend
 * @ZephyrId MAGETWO-12520
 *
 * @package Magento\Sales\Test\TestCase
 */
class OrderCreateTest extends Functional
{
    /**
     * Login to backend as a precondition to test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Test for creating order on backend
     */
    public function testCreateOrder()
    {
        $orderFixture = Factory::getFixtureFactory()->getMagentoSalesOrder();

        $this->_proceedToOrderCreatePage();

        $this->_fillOrderData($orderFixture);

        $this->_checkOrderAndCustomer($orderFixture);
    }

    /**
     * Test steps to go to create order page
     */
    protected function _proceedToOrderCreatePage()
    {
        $orderGridPage = Factory::getPageFactory()->getAdminSalesOrder();
        $gridPageActionsBlock = $orderGridPage->getPageActionsBlock();

        $orderGridPage->open();
        $gridPageActionsBlock->clickAddNew();
    }

    /**
     * Filling the order data from fixture and save the order
     *
     * @param Order $fixture
     */
    protected function _fillOrderData(Order $fixture)
    {
        $orderCreatePage = Factory::getPageFactory()->getAdminSalesOrderCreateIndex();
        $customerSelectionGrid = $orderCreatePage->getOrderCustomerBlock();
        $storeViewSelectionBlock = $orderCreatePage->getSelectStoreViewBlock();
        $itemsOrderedGrid = $orderCreatePage->getItemsOrderedGrid();
        $productsAddGrid = $orderCreatePage->getItemsAddGrid();
        $billingAddressForm = $orderCreatePage->getBillingAddressForm();
        $shippingAddressForm = $orderCreatePage->getShippingAddressForm();
        $paymentMethodsBlock = $orderCreatePage->getPaymentMethodsBlock();
        $shippingMethodsBlock = $orderCreatePage->getShippingMethodsBlock();
        $orderSummaryBlock = $orderCreatePage->getOrderSummaryBlock();
        $templateBlock = $orderCreatePage->getTemplateBlock();

        $customerSelectionGrid->selectCustomer($fixture);

        if ($storeViewSelectionBlock->isVisible())
        {
            $storeViewSelectionBlock->selectStoreView($fixture);
            $templateBlock->waitLoader();
        }

        $itemsOrderedGrid->addNewProduct();

        /** @var $product Product */
        foreach ($fixture->getProducts() as $product)
        {
            $productsAddGrid->addProduct($product);
        }
        $productsAddGrid->addSelectedProducts();

        $billingAddressForm->fill($fixture->getBillingAddress());
        $templateBlock->waitLoader();
        $shippingAddressForm->setSameAsBillingShippingAddress();
        $templateBlock->waitLoader();

        $paymentMethodsBlock->selectPaymentMethod($fixture);
        $templateBlock->waitLoader();
        $shippingMethodsBlock->selectShippingMethod($fixture);
        $templateBlock->waitLoader();

        $orderSummaryBlock->clickSaveOrder();
    }

    /**
     * Check order's grand total
     *
     * @param Order $fixture
     */
    protected function _checkOrderAndCustomer(Order $fixture)
    {
        $orderViewPage = Factory::getPageFactory()->getAdminSalesOrderView();
        $orderGridPage = Factory::getPageFactory()->getAdminSalesOrder();
        $orderGrid = $orderGridPage->getOrderGridBlock();

        $email = $orderViewPage->getOrderCustomerInformationBlock()->getCustomerEmail();
        $orderId = substr($orderViewPage->getTitleBlock()->getTitle(), 1);
        $grandTotal = $orderViewPage->getOrderTotalsBlock()->getGrandTotal();
        $orderGridPage->open();

        $orderGrid->searchAndOpen(array(
            'id' => $orderId
        ));
        $this->assertEquals($fixture->getGrandTotal(), $grandTotal);

        $customerGridPage = Factory::getPageFactory()->getAdminCustomer();
        $customerGrid = $customerGridPage->getCustomerGridBlock();
        $customerViewPage = Factory::getPageFactory()->getAdminCustomerEdit();

        $customerGridPage->open();
        $customerGrid->searchAndOpen(array(
            'email' => $email
        ));
        $customerPageTitle = $customerViewPage->getTitleBlock()->getTitle();

        $firstname = $fixture->getBillingAddress()->getFirstName()['value'];
        $lastname = $fixture->getBillingAddress()->getLastName()['value'];

        $this->assertEquals($customerPageTitle,  $firstname . ' ' . $lastname);
    }
}
