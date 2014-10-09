<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Sales\Test\Fixture\Order;

/**
 * Class CreateOrderTest
 * Tests for creating order on backend
 *
 */
class CreateOrderTest extends Functional
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
        //Data
        $fixture->persist();
        //Page
        $orderCreatePage = Factory::getPageFactory()->getSalesOrderCreateIndex();
        $orderGridPage = Factory::getPageFactory()->getSalesOrder();
        //Steps
        $orderGridPage->open();
        $orderCreatePage->getActionsBlock()->addNew();
        $orderCreatePage->getCustomerBlock()->selectCustomer($fixture->getCustomer());
        $orderCreatePage->getStoreBlock()->selectStoreView();

        $orderCreateBlock = $orderCreatePage->getCreateBlock();
        $orderCreateBlock->waitOrderItemsGrid();
        $orderCreateBlock->addProducts($fixture->getProducts());
        $billingAddress = $fixture->getBillingAddress();
        if (empty($billingAddress)) {
            $billingAddress = $fixture->getCustomer()->getDefaultBillingAddress();
        }
        $orderCreateBlock->fillAddresses($billingAddress);
        $orderCreateBlock->selectShippingMethod($fixture->getShippingMethod()->getData('fields'));
        $orderCreateBlock->selectPaymentMethod(['method' => $fixture->getPaymentMethod()->getPaymentCode()]);
        $orderCreateBlock->submitOrder();
        //Verification
        $this->_checkOrderAndCustomer($fixture);
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
        $email = $orderViewPage->getInformationBlock()->getCustomerEmail();
        $orderId = substr($orderViewPage->getTitleBlock()->getTitle(), 1);
        $grandTotal = $orderViewPage->getOrderTotalsBlock()->getGrandTotal();
        //Test flow - order grand total check
        $orderGridPage->open();
        $orderGrid->searchAndOpen(['id' => $orderId]);
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
        $customerGridPage = Factory::getPageFactory()->getCustomerIndex();
        $customerViewPage = Factory::getPageFactory()->getCustomerIndexEdit();
        //Block
        $customerGrid = $customerGridPage->getCustomerGridBlock();

        //Test flow - customer saved check
        $customerGridPage->open();
        $customerGrid->searchAndOpen(['email' => $email]);
        $customerPageTitle = $customerViewPage->getTitleBlock()->getTitle();

        $customer = $fixture->getCustomer();
        if (!empty($customer)) {
            $firstName = $fixture->getCustomer()->getFirstName();
            $lastName = $fixture->getCustomer()->getLastName();
        } else {
            $firstName = $fixture->getBillingAddress()->getFirstName()['value'];
            $lastName = $fixture->getBillingAddress()->getLastName()['value'];
        }

        $this->assertEquals($customerPageTitle, $firstName . ' ' . $lastName);
    }

    /**
     * @return array
     */
    public function dataProviderOrderFixtures()
    {
        return [
            [Factory::getFixtureFactory()->getMagentoSalesOrderWithCustomer()],
            [Factory::getFixtureFactory()->getMagentoSalesOrder()]
        ];
    }
}
