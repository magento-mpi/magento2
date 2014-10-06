<?php
/**
 * {license_notice}
 *
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
        $this->markTestIncomplete('MAGETWO-28220');
        $fixture->persist();

        //Ensure shopping cart is empty
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCartIndex();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();

        //Add products to cart
        $products = $fixture->getProducts();
        foreach ($products as $product) {
            $productPage = Factory::getPageFactory()->getCatalogProductView();
            Factory::getClientBrowser()->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
            $productPage->getViewBlock()->addToCart($product);
            Factory::getPageFactory()->getCheckoutCartIndex()->getMessagesBlock()->waitSuccessMessage();
        }

        //Proceed to checkout
        $checkoutCartPage = Factory::getPageFactory()->getMultishippingCheckoutCart();
        $checkoutCartPage->getMultishippingLinkBlock()->multipleAddressesCheckout();

        //Register new customer
        Factory::getPageFactory()->getMultishippingCheckoutLogin()->getLoginBlock()->registerCustomer();
        $multishippingRegisterPage = Factory::getPageFactory()->getMultishippingCheckoutRegister();
        $multishippingRegisterPage->getRegisterBlock()
            ->registerCustomer($fixture->getCustomer(), $fixture->getCustomer()->getDefaultBillingAddress());

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
        $payment = [
            'method' => $fixture->getPaymentMethod()->getPaymentCode(),
            'dataConfig' => $fixture->getPaymentMethod()->getDataConfig(),
            'credit_card' => $fixture->getCreditCard(),
        ];
        Factory::getPageFactory()->getMultishippingCheckoutBilling()->getBillingBlock()->selectPaymentMethod($payment);
        Factory::getPageFactory()->getMultishippingCheckoutOverview()->getOverviewBlock()->placeOrder($fixture);

        //Verify order in Backend
        $successPage = Factory::getPageFactory()->getMultishippingCheckoutSuccess();
        $orderIds = $successPage->getSuccessBlock()->getOrderIds($fixture);
        $this->_verifyOrder($orderIds, $fixture);
    }

    /**
     * @return array
     */
    public function dataProviderMultishippingCheckout()
    {
        return [
            [Factory::getFixtureFactory()->getMagentoMultishippingGuestPaypalDirect()],
        ];
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
            $orderPage->getOrderGridBlock()->searchAndOpen(['id' => $orderId]);
            $this->assertEquals(
                $grandTotals[$num],
                Factory::getPageFactory()->getSalesOrderView()->getOrderTotalsBlock()->getGrandTotal(),
                'Incorrect grand total value for the order #' . $orderId
            );
        }
    }
}
