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

namespace Scenarios\Test\TestCase\Checkout;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class MultishippingCheckoutTest
 * Test multiple address page checkout with different configurations
 *
 * @package Scenarios\Test\TestCase\Checkout
 */
class MultishippingCheckoutTest extends Functional
{
    /**
     * Place order on frontend via multishipping.
     *
     * @param Checkout $fixture
     * @dataProvider dataProviderMultishippingCheckout
     */
    public function testMultishippingCheckout(Checkout $fixture)
    {
        //Add products to cart
        $products = $fixture->getProducts();
        foreach ($products as $product) {
            $productPage = Factory::getPageFactory()->getCatalogProductView();
            $productPage->init($product);
            $productPage->open();
            $productPage->getViewBlock()->addToCart($product);
            Factory::getPageFactory()->getCheckoutCart()->getCartBlock()->waitForProductAdded();
        }

        //Proceed to checkout
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        //Multishipping checkout
        $checkoutCartPage->getCartBlock()->getMultishippingLinkBlock()->multipleAddressesCheckout();

        //Register new customer
        Factory::getPageFactory()->getCheckoutMultishippingLogin()->getLoginBlock()->registerCustomer();
        $multishippingRegisterPage = Factory::getPageFactory()->getCheckoutMultishippingRegister();
        //Hack. Opening of this page must be removed when https://jira.corp.x.com/browse/MAGETWO-16318 will be fixed
        $multishippingRegisterPage->open();
        $multishippingRegisterPage->getRegisterBlock()->registerCustomer($fixture->getCustomer());

        //Mapping products and shipping addresses
        if ($fixture->getNewShippingAddresses()) {
            foreach ($fixture->getNewShippingAddresses() as $address) {
                Factory::getPageFactory()->getCheckoutMultishippingAddresses()->getAddressesBlock()->addNewAddress();
                Factory::getPageFactory()->getCheckoutMultishippingAddressNewShipping()->getAddressesEditBlock()
                    ->editCustomerAddress($address);
            }
        }
        Factory::getPageFactory()->getCheckoutMultishippingAddresses()->getAddressesBlock()->selectAddresses($fixture);

        //Select shipping and payment methods
        Factory::getPageFactory()->getCheckoutMultishippingShipping()->getShippingBlock()
            ->selectShippingMethod($fixture);
        Factory::getPageFactory()->getCheckoutMultishippingBilling()->getBillingBlock()->selectPaymentMethod($fixture);
        Factory::getPageFactory()->getCheckoutMultishippingOverview()->getOverviewBlock()->placeOrder($fixture);

        //Verify order in Backend TODO assert constraints
        $orderIds = Factory::getPageFactory()->getCheckoutMultishippingSuccess()->getSuccessBlock()
            ->getOrderIds($fixture);
        Factory::getApp()->magentoBackendLoginUser();
        $grandTotals = $fixture->getGrandTotal();
        foreach ($orderIds as $num => $orderId) {
            $orderPage = Factory::getPageFactory()->getAdminSalesOrder();
            $orderPage->open();
            $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
            $this->assertContains(
                $grandTotals[$num],
                Factory::getPageFactory()->getAdminSalesOrderView()->getOrderTotalsBlock()->getGrandTotal(),
                'Incorrect grand total value for the order #' . $orderId
            );
        }
    }

    /**
     * @return array
     */
    public function dataProviderMultishippingCheckout()
    {
        return array(
            array(Factory::getFixtureFactory()->getMagentoCheckoutMultishippingGuestPaypalDirect()),
        );
    }
}
