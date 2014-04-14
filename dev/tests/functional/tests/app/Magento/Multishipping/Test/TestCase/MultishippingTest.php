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

namespace Magento\Multishipping\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Multishipping\Test\Fixture\GuestPaypalDirect;

/**
 * Class MultishippingTest
 * Test multiple address page checkout with different configurations
 *
 * @package Magento\Multishipping\Test\TestCase
 */
class MultishippingTest extends Functional
{
    /**
     * Place order on frontend via multishipping.
     *
     * @param GuestPaypalDirect $fixture
     * @dataProvider dataProviderMultishippingCheckout
     *
     * @ZephyrId MAGETWO-12836
     */
    public function testMultishippingCheckout(GuestPaypalDirect $fixture)
    {
        $fixture->persist();

        //Ensure shopping cart is empty
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();

        //Add products to cart
        $products = $fixture->getProducts();
        foreach ($products as $product) {
            $productPage = Factory::getPageFactory()->getCatalogProductView();
            $productPage->init($product);
            $productPage->open();
            $productPage->getViewBlock()->addToCart($product);
            Factory::getPageFactory()->getCheckoutCart()->getMessageBlock()->assertSuccessMessage();
        }

        //Proceed to checkout
        $checkoutCartPage = Factory::getPageFactory()->getMultishippingCheckoutCart();
        $checkoutCartPage->getMultishippingLinkBlock()->multipleAddressesCheckout();

        //Register new customer
        Factory::getPageFactory()->getMultishippingCheckoutLogin()->getLoginBlock()->registerCustomer();
        $multishippingRegisterPage = Factory::getPageFactory()->getMultishippingCheckoutRegister();
        $multishippingRegisterPage->getRegisterBlock()->registerCustomer($fixture->getCustomer());

        //Mapping products and shipping addresses
        if ($fixture->getNewShippingAddresses()) {
            foreach ($fixture->getNewShippingAddresses() as $address) {
                Factory::getPageFactory()->getMultishippingCheckoutAddresses()->getAddressesBlock()->addNewAddress();
                Factory::getPageFactory()->getMultishippingCheckoutAddressNewShipping()->getEditBlock()
                    ->editCustomerAddress($address);
            }
        }
        Factory::getPageFactory()->getMultishippingCheckoutAddresses()->getAddressesBlock()->selectAddresses($fixture);

        //Select shipping and payment methods
        Factory::getPageFactory()->getMultishippingCheckoutShipping()->getShippingBlock()
            ->selectShippingMethod($fixture);
        Factory::getPageFactory()->getMultishippingCheckoutBilling()->getBillingBlock()->selectPaymentMethod($fixture);
        Factory::getPageFactory()->getMultishippingCheckoutOverview()->getOverviewBlock()->placeOrder($fixture);

        //Verify order in Backend
        $successPage = Factory::getPageFactory()->getMultishippingCheckoutSuccess();
        $this->assertContains(
            'Your order has been received.',
            $successPage->getTitleBlock()->getTitle(),
            'Order success page was not opened.');
        $orderIds = $successPage->getSuccessBlock()->getOrderIds($fixture);
        $this->_verifyOrder($orderIds, $fixture);
    }

    /**
     * @return array
     */
    public function dataProviderMultishippingCheckout()
    {
        return array(
            array(Factory::getFixtureFactory()->getMagentoMultishippingGuestPaypalDirect()),
        );
    }

    /**
     * Verify order in Backend
     *
     * @param array $orderIds
     * @param GuestPaypalDirect $fixture
     */
    protected function _verifyOrder($orderIds, GuestPaypalDirect $fixture)
    {
        Factory::getApp()->magentoBackendLoginUser();
        $grandTotals = $fixture->getGrandTotal();
        foreach ($orderIds as $num => $orderId) {
            $orderPage = Factory::getPageFactory()->getSalesOrder();
            $orderPage->open();
            $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
            $this->assertContains(
                $grandTotals[$num],
                Factory::getPageFactory()->getSalesOrderView()->getOrderTotalsBlock()->getGrandTotal(),
                'Incorrect grand total value for the order #' . $orderId
            );
        }
    }
}
