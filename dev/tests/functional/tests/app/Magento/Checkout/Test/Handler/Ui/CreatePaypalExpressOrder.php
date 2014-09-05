<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Handler\Ui;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Ui;
use Mtf\Factory\Factory;

/**
 * Class CreatePaypalExpressOrder
 * Create a product
 *
 */
class CreatePaypalExpressOrder extends Ui
{
    /**
     * Create product
     *
     * @param FixtureInterface $fixture [optional]
     * @return mixed|string
     */
    public function persist(FixtureInterface $fixture = null)
    {
        //Ensure shopping cart is empty
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCartIndex();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();

        $products = $fixture->getProducts();

        foreach ($products as $product) {
            $productPage = Factory::getPageFactory()->getCatalogProductView();
            Factory::getClientBrowser()->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
            $productPage->getViewBlock()->addToCart($product);
        }

        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCartIndex();
        $checkoutCartPage->getCartBlock()->paypalCheckout();

        $paypalCustomer = $fixture->getPaypalCustomer();
        $paypalPage = Factory::getPageFactory()->getPaypal();
        $paypalPage->getLoginExpressBlock()->login($paypalCustomer);
        $paypalPage->getReviewExpressBlock()->continueCheckout();

        $checkoutReviewPage = Factory::getPageFactory()->getPaypalExpressReview();
        $checkoutReviewPage->getReviewBlock()->selectShippingMethod($fixture->getShippingMethods());
        $checkoutReviewPage->getReviewBlock()->placeOrder();

        $orderId = Factory::getPageFactory()->getCheckoutOnepageSuccess()->getSuccessBlock()->getOrderId($fixture);

        return $orderId;
    }
}
