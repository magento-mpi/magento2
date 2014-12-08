<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

use Magento\Sales\Test\Fixture\Order;
use Mtf\Factory\Factory;
use Mtf\ObjectManager;
use Mtf\TestCase\Functional;

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

        $addProductsStep = $this->objectManager->create(
            'Magento\Sales\Test\TestStep\AddProductsStep',
            ['products' => $fixture->getProducts()]
        );
        $addProductsStep->run();

        $billingAddress = $fixture->getBillingAddress();
        if (empty($billingAddress)) {
            $billingAddress = $fixture->getCustomer()->getDefaultBillingAddress();
        }
        $orderCreateBlock->fillAddresses($billingAddress);
        $orderCreateBlock->selectShippingMethod($fixture->getShippingMethod()->getData('fields'));
        $orderCreateBlock->selectPaymentMethod(['method' => $fixture->getPaymentMethod()->getPaymentCode()]);
        $orderCreateBlock->submitOrder();
        //Verification
        $this->checkOrderAndCustomer($fixture);
    }

    /**
     * Check order's grand total
     *
     * @param Order $fixture
     */
    protected function checkOrderAndCustomer(Order $fixture)
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
        $this->checkCustomer($fixture, $email);
    }

    /**
     * Check that customer exists
     *
     * @param Order $fixture
     * @param string $email
     */
    protected function checkCustomer($fixture, $email)
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
