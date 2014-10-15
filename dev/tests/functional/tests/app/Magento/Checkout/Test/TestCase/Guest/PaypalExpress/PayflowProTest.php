<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\TestCase\Guest\PaypalExpress;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class OnepageCheckoutTest
 * Tests checkout via Magento one page checkout and Paypal Express checkout.
 * Shipping method used is Flat Rate
 *
 */
class PayflowProTest extends Functional
{
    /**
     * Guest checkout using "Checkout with PayPal" button from the shopping cart and offline shipping method
     *
     * @ZephyrId MAGETWO-12414
     */
    public function testPayflowProExpress()
    {
        $fixture = Factory::getFixtureFactory()->getMagentoCheckoutExpressPayPalPayflow();
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

        //Proceed to PayPal
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCartIndex();
        $checkoutCartPage->getCartBlock()->paypalCheckout();

        //Proceed Checkout on PayPal side
        $paypalCustomer = $fixture->getPaypalCustomer();
        $paypalPage = Factory::getPageFactory()->getPaypal();
        $paypalPage->getLoginExpressBlock()->login($paypalCustomer);
        $paypalPage->getReviewExpressBlock()->continueCheckout();

        //Proceed Checkout on Magento side
        $checkoutReviewPage = Factory::getPageFactory()->getPaypalExpressReview();
        $checkoutReviewPage->getReviewBlock()->selectShippingMethod($fixture->getShippingMethods());
        $checkoutReviewPage->getReviewBlock()->placeOrder();

        //Verify order in Backend
        $successPage = Factory::getPageFactory()->getCheckoutOnepageSuccess();
        $orderId = $successPage->getSuccessBlock()->getOrderId($fixture);
        $this->_verifyOrder($orderId, $fixture);
    }

    /**
     * Verify order in backend
     *
     * @param string $orderId
     * @param Checkout $fixture
     */
    protected function _verifyOrder($orderId, Checkout $fixture)
    {
        Factory::getApp()->magentoBackendLoginUser();
        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(['id' => $orderId]);
        $this->assertContains(
            $fixture->getGrandTotal(),
            Factory::getPageFactory()->getSalesOrderView()->getOrderTotalsBlock()->getGrandTotal(),
            'Incorrect grand total value for the order #' . $orderId
        );
        $expectedAuthorizedAmount = 'Authorized amount of $' . $fixture->getGrandTotal();

        $actualAuthorizedAmount = Factory::getPageFactory()->getSalesOrderView()
            ->getOrderHistoryBlock()->getCommentsHistory();
        $this->assertContains(
            $expectedAuthorizedAmount,
            $actualAuthorizedAmount,
            'Incorrect authorized amount value for the order #' . $orderId
        );
    }
}
