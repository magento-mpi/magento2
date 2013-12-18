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
     * Guest checkout using "Checkout with PayPal" button from the shopping cart and offline shipping method
     *
     * @ZephyrId MAGETWO-12414
     */
    public function testPayflowProExpress()
    {
        $fixture = Factory::getFixtureFactory()->getMagentoCheckoutExpressPayPalPayflow();
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

        //Proceed to PayPal
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->getCartBlock()->paypalCheckout();

        //Proceed Checkout on PayPal side
        $paypalCustomer = $fixture->getPaypalCustomer();
        $paypalPage = Factory::getPageFactory()->getPaypal();
        $paypalPage->getLoginBlock()->login($paypalCustomer);
        $paypalPage->getReviewBlock()->continueCheckout();

        //Proceed Checkout on Magento side
        $checkoutReviewPage = Factory::getPageFactory()->getPaypalukExpressReview();
        $checkoutReviewPage->getReviewBlock()->selectShippingMethod($fixture->getShippingMethods());
        $checkoutReviewPage->getReviewBlock()->placeOrder();

        //Verify order in Backend
        $successPage = Factory::getPageFactory()->getCheckoutOnepageSuccess();
        $this->assertContains(
            'Your order has been received.',
            $successPage->getTitleBlock()->getTitle(),
            'Order success page was not opened.');
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
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
        $this->assertContains(
            $fixture->getGrandTotal(),
            Factory::getPageFactory()->getSalesOrderView()->getOrderTotalsBlock()->getGrandTotal(),
            'Incorrect grand total value for the order #' . $orderId
        );
        $expectedAuthorizedAmount = 'Authorized amount of ' . $fixture->getGrandTotal();

        $actualAuthorizedAmount = Factory::getPageFactory()->getSalesOrderView()
            ->getOrderHistoryBlock()->getCommentsHistory();
        $this->assertContains(
            $expectedAuthorizedAmount,
            $actualAuthorizedAmount,
            'Incorrect authorized amount value for the order #' . $orderId
        );
    }
}
