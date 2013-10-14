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

class ExpressCheckoutTest extends Functional
{
    /**
     * Place order on frontend via express checkout.
     *
     * @param Checkout $fixture
     * @dataProvider dataProviderExpressCheckout
     */
    public function testExpressCheckout(Checkout $fixture)
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
        Factory::getPageFactory()->getCheckoutCart()->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();

        //Proceed Checkout
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->getLoginBlock()->checkoutMethod($fixture);
        $checkoutOnePage->getBillingBlock()->fillBilling($fixture);
        $checkoutOnePage->getShippingMethodBlock()->selectShippingMethod($fixture);
        $checkoutOnePage->getPaymentMethodsBlock()->selectPaymentMethod($fixture);

        $paypalPage = Factory::getPageFactory()->getPaypal();
        $paypalPage->getLoginBlock()->login($fixture->getPaypalCustomer());
        $paypalPage->getReviewBlock()->continueCheckout();

        $checkoutReviewPage = Factory::getPageFactory()->getPaypalExpressReview();
        $checkoutReviewPage->getReviewBlock()->verifyOrderInformation($fixture);
        $checkoutReviewPage->getReviewBlock()->placeOrder();

        //Verify order in Backend TODO assert constraints
        $orderId = Factory::getPageFactory()->getCheckoutOnepageSuccess()->getSuccessBlock()->getOrderId($fixture);
        Factory::getApp()->magentoBackendLoginUser();
        $orderPage = Factory::getPageFactory()->getAdminSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
        $this->assertContains(
            $fixture->getGrandTotal(),
            Factory::getPageFactory()->getAdminSalesOrderView()->getOrderTotalsBlock()->getGrandTotal(),
            'Incorrect grand total value for the order #' . $orderId
        );
    }

    /**
     * @return array
     */
    public function dataProviderExpressCheckout()
    {
        return array(
            array(Factory::getFixtureFactory()->getMagentoCheckoutGuestPaypalExpress()),
        );
    }

    /**
     * Place order on frontend via express checkout.
     *
     * @param Checkout $fixture
     * @dataProvider dataProviderExpressCheckoutFromProductPage
     */
    public function testExpressCheckoutFromProductPage(Checkout $fixture)
    {
        $products = $fixture->getProducts();
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->init($products[0]);
        $productPage->open();

        //Proceed to checkout
        $productPage->getViewBlock()->paypalCheckout();

        //Proceed Checkout
        $paypalPage = Factory::getPageFactory()->getPaypal();
        $paypalPage->getLoginBlock()->login($fixture->getPaypalCustomer());
        $paypalPage->getReviewBlock()->continueCheckout();

        $checkoutReviewPage = Factory::getPageFactory()->getPaypalExpressReview();
        $checkoutReviewPage->getReviewBlock()->verifyOrderInformation($fixture);
        $checkoutReviewPage->getReviewBlock()->getShippingBlock()->setTelephoneNumber($fixture->getTelephoneNumber());
        $checkoutReviewPage->getReviewBlock()->selectShippingMethod($fixture);
        $checkoutReviewPage->getReviewBlock()->placeOrder();

        //Verify order in Backend TODO assert constraints
        $orderId = Factory::getPageFactory()->getCheckoutOnepageSuccess()->getSuccessBlock()->getOrderId($fixture);
        Factory::getApp()->magentoBackendLoginUser();
        $orderPage = Factory::getPageFactory()->getAdminSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
        $this->assertContains(
            $fixture->getGrandTotal(),
            Factory::getPageFactory()->getAdminSalesOrderView()->getOrderTotalsBlock()->getGrandTotal(),
            'Incorrect grand total value for the order #' . $orderId
        );
    }

    /**
     * @return array
     */
    public function dataProviderExpressCheckoutFromProductPage()
    {
        return array(
            array(Factory::getFixtureFactory()->getMagentoCheckoutPaypalExpress()),
        );
    }
}
