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
class CheckoutOnepageTest extends Functional
{
    /**
     * Place order on frontend via one page checkout and Paypal Express checkout.
     * Shipping method used is Flat Rate
     */
    public function testOnepageCheckout()
    {
        $fixture = Factory::getFixtureFactory()->getMagentoCheckoutGuestPaypalExpress();
        $fixture->persist();

        $this->_createAndAddProducts($fixture);

        $this->_magentoCheckoutProcess($fixture);

        $this->_processPaypal($fixture);

        $this->_reviewOrder();

        $this->_verifyOrder($fixture);

    }

    /**
     * Create and add to cart products
     *
     * @param Checkout $fixture
     */
    protected function _createAndAddProducts(Checkout $fixture)
    {
        $products = $fixture->getProducts();
        foreach ($products as $product) {
            $productPage = Factory::getPageFactory()->getCatalogProductView();
            $productPage->init($product);
            $productPage->open();
            $productPage->getViewBlock()->addToCart($product);
        }
    }

    /**
     * Process Magento Checkout
     *
     * @param Checkout $fixture
     */
    protected function _magentoCheckoutProcess(Checkout $fixture)
    {
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();

        //Proceed Checkout
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->getLoginBlock()->checkoutMethod($fixture);
        $checkoutOnePage->getBillingBlock()->fillBilling($fixture);
        $checkoutOnePage->getShippingMethodBlock()->selectShippingMethod($fixture);
        $checkoutOnePage->getPaymentMethodsBlock()->selectPaymentMethod($fixture);
    }

    /**
     * Process paypal login and continue back to Magento
     *
     * @param Checkout $fixture
     */
    protected function _processPaypal(Checkout $fixture)
    {
        $paypalCustomer = $fixture->getPaypalCustomer();
        $paypalPage = Factory::getPageFactory()->getPaypal();
        $paypalPage->getLoginBlock()->login($paypalCustomer);
        $paypalPage->getReviewBlock()->continueCheckout();
    }

    /**
     * Review order on checkout Magento page and place it
     */
    protected function _reviewOrder()
    {
        $checkoutReviewPage = Factory::getPageFactory()->getPaypalExpressReview();
        $checkoutReviewPage->getReviewBlock()->placeOrder();
    }

    /**
     * Verify created order on backend
     *
     * @param Checkout $fixture
     */
    protected function _verifyOrder(Checkout $fixture)
    {
        $orderId = Factory::getPageFactory()->getCheckoutOnepageSuccess()->getSuccessBlock()->getGuestOrderId();
        Factory::getApp()->magentoBackendLoginUser();
        $orderPage = Factory::getPageFactory()->getAdminSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));

        $this->assertContains(
            $fixture->getGrandTotal(),
            Factory::getPageFactory()->getAdminSalesOrderView()->getOrderTotalsBlock()->getGrandTotal(),
            'Incorrect grand total value for the order #' . $orderId);
        $this->assertContains(
            $fixture->getGrandTotal(),
            Factory::getPageFactory()->getAdminSalesOrderView()->getOrderHistoryBlock()->getCommentsHistory(),
            'Incorrect authorized amount value for the order #' . $orderId);
    }
}
