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

/**
 * Tests for creating order on backend
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
     *
     * @param Order $fixture
     * @dataProvider dataProviderOrderFixtures
     * @ZephyrId MAGETWO-12520, MAGETWO-12395
     */
    public function testCreateOrder(Order $fixture)
    {
        //Init data
        $fixture->persist();
        //Steps
        $this->_proceedToOrderCreatePage();
        $this->_fillOrderData($fixture);
        //Verification
        $this->_checkOrderAndCustomer($fixture);
    }

    /**
     * Test steps to go to create order page
     */
    protected function _proceedToOrderCreatePage()
    {
        $orderGridPage = Factory::getPageFactory()->getSalesOrder();
        $gridPageActionsBlock = $orderGridPage->getOrderActionsBlock();

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
        //Blocks initialization
        $customerSelectionGrid = $orderCreatePage->getOrderCustomerBlock();
        $storeViewSelectionBlock = $orderCreatePage->getSelectStoreViewBlock();
        $itemsOrderedGrid = $orderCreatePage->getItemsOrderedGrid();
        $productsAddGrid = $orderCreatePage->getItemsAddGrid();
        $addressesBlock = $orderCreatePage->getAddressesBlock();
        $paymentMethodsBlock = $orderCreatePage->getPaymentMethodsBlock();
        $shippingMethodsBlock = $orderCreatePage->getShippingMethodsBlock();
        $orderSummaryBlock = $orderCreatePage->getOrderSummaryBlock();
        //Test flow
        $customerSelectionGrid->selectCustomer($fixture);
        $storeViewSelectionBlock->selectStoreView($fixture);
        $itemsOrderedGrid->addNewProducts();
        $productsAddGrid->addProducts($fixture);
        $addressesBlock->fillAddresses($fixture);
        $paymentMethodsBlock->selectPaymentMethod($fixture);
        $shippingMethodsBlock->selectShippingMethod($fixture);
        $orderSummaryBlock->clickSaveOrder();
    }

    /**
     * Check order's grand total
     *
     * @param Order $fixture
     */
    protected function _checkOrderAndCustomer(Order $fixture)
    {
        $orderViewPage = Factory::getPageFactory()->getSalesOrderView();
        $orderGridPage = Factory::getPageFactory()->getSalesOrder();
        $orderGrid = $orderGridPage->getOrderGridBlock();
        //Verification data
        $email = $orderViewPage->getOrderCustomerInformationBlock()->getCustomerEmail();
        $orderId = substr($orderViewPage->getTitleBlock()->getTitle(), 1);
        $grandTotal = $orderViewPage->getOrderTotalsBlock()->getGrandTotal();
        //Test flow - order grand total check
        $orderGridPage->open();
        $orderGrid->searchAndOpen(array(
            'id' => $orderId
        ));
        $this->assertEquals($fixture->getGrandTotal(), $grandTotal);
        $this->_checkCustomer($fixture, $email);
    }

    /**
     * Check that customer exists
     *
     * @param Order $fixture
     * @param string $email
     */
    protected function _checkCustomer($fixture, $email)
    {
        //Pages
        $customerGridPage = Factory::getPageFactory()->getAdminCustomer();
        $customerViewPage = Factory::getPageFactory()->getAdminCustomerEdit();
        //Block
        $customerGrid = $customerGridPage->getCustomerGridBlock();

        //Test flow - customer saved check
        $customerGridPage->open();
        $customerGrid->searchAndOpen(array(
            'email' => $email
        ));
        $customerPageTitle = $customerViewPage->getTitleBlock()->getTitle();

        $customer = $fixture->getCustomer();
        if (!empty($customer)) {
            $firstName = $fixture->getCustomer()->getFirstName();
            $lastName = $fixture->getCustomer()->getLastName();
        } else {
            $firstName = $fixture->getBillingAddress()->getFirstName()['value'];
            $lastName = $fixture->getBillingAddress()->getLastName()['value'];
        }

        $this->assertEquals($customerPageTitle,  $firstName . ' ' . $lastName);
    }

    /**
     * @return array
     */
    public function dataProviderOrderFixtures()
    {
        return array(
            array(Factory::getFixtureFactory()->getMagentoSalesOrderWithCustomer()),
            array(Factory::getFixtureFactory()->getMagentoSalesOrder())
        );
    }
}
