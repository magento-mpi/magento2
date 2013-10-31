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

namespace Magento\Checkout\Test\TestCase\Guest\PaypalExpress;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class OnepageCheckoutTest
 * Tests checkout via Magento one page checkout and Paypal Express checkout.
 * Shipping method used is Flat Rate
 *
 * @package Magento\Test\TestCase\Checkout
 */
class PayflowProTest extends Functional
{
    /**
     * Place order on frontend via one page checkout and Paypal Express checkout.
     * Shipping method used is Flat Rate
     */
    public function testPayflowProExpress()
    {
        $this->markTestSkipped('MAGETWO-16653');
        $fixture = Factory::getFixtureFactory()->getMagentoCheckoutExpressPayPalPayflow();
        $fixture->persist();

        $products = $fixture->getProducts();
        foreach ($products as $product) {
            $productPage = Factory::getPageFactory()->getCatalogProductView();
            $productPage->init($product);
            $productPage->open();
            $productPage->getViewBlock()->addToCart($product);
        }

        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->getCartBlock()->paypalCheckout();

        $paypalCustomer = $fixture->getPaypalCustomer();
        $paypalPage = Factory::getPageFactory()->getPaypal();
        $paypalPage->getLoginBlock()->login($paypalCustomer);
        $paypalPage->getReviewBlock()->continueCheckout();

        $checkoutReviewPage = Factory::getPageFactory()->getPaypalukExpressReview();
        $checkoutReviewPage->getReviewBlock()->selectShippingMethod($fixture->getShippingMethods());

        $checkoutReviewPage->getReviewBlock()->placeOrder();

        $orderId = Factory::getPageFactory()->getCheckoutOnepageSuccess()->getSuccessBlock()->getGuestOrderId();
        //Backend
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
        $orderPage = Factory::getPageFactory()->getAdminSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
        $this->assertContains(
            $fixture->getGrandTotal(),
            Factory::getPageFactory()->getAdminSalesOrderView()->getOrderTotalsBlock()->getGrandTotal(),
            'Incorrect grand total value for the order #' . $orderId
        );
        $expectedAuthorizedAmount = 'Authorized amount of ' . $fixture->getGrandTotal();

        $actualAuthorizedAmount = Factory::getPageFactory()->getAdminSalesOrderView()
            ->getOrderHistoryBlock()->getCommentsHistory();
        $this->assertContains(
            $expectedAuthorizedAmount,
            $actualAuthorizedAmount,
            'Incorrect authorized amount value for the order #' . $orderId
        );
    }
}
