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
 * Class ProductPageTest
 * Place order via Express Checkout from product page
 *
 */
class ProductPageTest extends Functional
{
    /**
     * Guest checkout using "Checkout with PayPal" button from product page and Free Shipping
     *
     * @ZephyrId MAGETWO-12415
     */
    public function testCheckoutFreeShipping()
    {
        $fixture = Factory::getFixtureFactory()->getMagentoCheckoutPaypalExpress();
        $fixture->persist();

        //Ensure shopping cart is empty
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCartIndex();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();

        //Open product page
        $products = $fixture->getProducts();
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        Factory::getClientBrowser()->open($_ENV['app_frontend_url'] . end($products)->getUrlKey() . '.html');

        //Proceed Checkout
        $productPage->getViewBlock()->paypalCheckout();
        $paypalPage = Factory::getPageFactory()->getPaypal();
        $paypalPage->getLoginExpressBlock()->login($fixture->getPaypalCustomer());
        $paypalPage->getReviewExpressBlock()->continueCheckout();
        $checkoutReviewPage = Factory::getPageFactory()->getPaypalExpressReview();
        $checkoutReviewPage->getReviewBlock()->selectShippingMethod($fixture->getShippingMethods());
        $checkoutReviewPage->getReviewBlock()->placeOrder();

        //Verification
        $successPage = Factory::getPageFactory()->getCheckoutOnepageSuccess();
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
        $orderPage->getOrderGridBlock()->searchAndOpen(['id' => $orderId]);

        $this->assertContains(
            $fixture->getGrandTotal(),
            Factory::getPageFactory()->getSalesOrderView()->getOrderTotalsBlock()->getGrandTotal(),
            'Incorrect grand total value for the order #' . $orderId
        );

        $this->assertContains(
            'Authorized amount of $' . $fixture->getGrandTotal(),
            Factory::getPageFactory()->getSalesOrderView()->getOrderHistoryBlock()->getCommentsHistory(),
            'Incorrect authorized amount value for the order #' . $orderId
        );
    }
}
