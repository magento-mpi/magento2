<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Magento
 * @package     Magento_Checkout
 * @subpackage  functional_tests
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
 * @package Magento\Checkout\Test\Handler\Ui
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

        $checkoutReviewPage = Factory::getPageFactory()->getPaypalExpressReview();
        $checkoutReviewPage->getReviewBlock()->fillTelephone($fixture->getTelephoneNumber());
        $checkoutReviewPage->getReviewBlock()->selectShippingMethod($fixture->getShippingMethods());

        $checkoutReviewPage->getReviewBlock()->placeOrder();

        $orderId = Factory::getPageFactory()->getCheckoutOnepageSuccess()->getSuccessBlock()->getOrderId($fixture);

        return $orderId;
    }
}
