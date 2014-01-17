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

namespace Magento\Checkout\Test\TestCase\Guest;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class OnepageTest
 * Test one page with PayPal Standard payment method
 *
 * @package Magento\Test\TestCase\Guest
 */
class PaypalStandardTest extends Functional
{
    /**
     * Guest checkout using PayPal Payments Standard method and offline shipping method
     *
     * @ZephyrId MAGETWO-12964
     */
    public function testOnepageCheckout()
    {
        $this->markTestSkipped('Bamboo inability to run tests on instance without public IP address.');

        /** @var Checkout $fixture */
        $fixture = Factory::getFixtureFactory()->getMagentoCheckoutGuestPayPalStandard();
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
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();


        //Proceed Checkout
        /** @var \Magento\Checkout\Test\Page\CheckoutOnepage $checkoutOnePage */
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->getLoginBlock()->checkoutMethod($fixture);
        $checkoutOnePage->getBillingBlock()->fillBilling($fixture);
        $checkoutOnePage->getShippingMethodBlock()->selectShippingMethod($fixture);
        $checkoutOnePage->getPaymentMethodsBlock()->selectPaymentMethod($fixture);

        $checkoutOnePage->getReviewBlock()->placeOrder();

        $paypalCustomer = $fixture->getPaypalCustomer();
        $paypalPage = Factory::getPageFactory()->getPaypal();
        $paypalPage->getBillingBlock()->clickLoginLink();
        $paypalPage->getLoginBlock()->login($paypalCustomer);
        $paypalPage->getReviewBlock()->continueCheckout();
        $paypalPage->getMainPanelBlock()->clickReturnLink();

        //Verify order in Backend
        /** @var \Magento\Checkout\Test\Page\CheckoutOnepageSuccess $successPage */
        $successPage = Factory::getPageFactory()->getCheckoutOnepageSuccess();
        $this->assertContains(
            'Your order has been received.',
            $successPage->getTitleBlock()->getTitle(),
            'Order success page was not opened.');

        /** @var  string $orderId */
        $orderId = $successPage->getSuccessBlock()->getOrderId($fixture);
        $this->_verifyOrder($orderId, $fixture);
    }

    /**
     * Verify order in Backend
     *
     * @param string $orderId
     * @param Checkout $fixture
     */
    protected function _verifyOrder($orderId, Checkout $fixture)
    {
        Factory::getApp()->magentoBackendLoginUser();
        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
        $this->assertContains(
            $fixture->getGrandTotal(),
            Factory::getPageFactory()->getSalesOrderView()->getOrderTotalsBlock()->getGrandTotal(),
            'Incorrect grand total value for the order #' . $orderId
        );

        $expectedAuthorizedAmount = 'captured amount of ' . $fixture->getGrandTotal();
        $this->assertContains(
            $expectedAuthorizedAmount,
            Factory::getPageFactory()->getSalesOrderView()->getOrderHistoryBlock()->getCapturedAmount(),
            'Incorrect captured amount value for the order #' . $orderId
        );
    }
}
