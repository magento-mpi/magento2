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

use Mtf\Fixture;
use Mtf\Handler\Ui;
use Mtf\Factory\Factory;

/**
 * Class CreateOrder
 * Create an order based on the fixture provided.
 *
 * @package Magento\Checkout\Test\Handler\Ui
 */
class CreateOrder extends Ui
{
    /**
     * Create order
     *
     * @param Fixture $fixture [optional]
     * @return mixed|string
     */
    public function execute(Fixture $fixture = null)
    {
        $products = $fixture->getProducts();

        foreach ($products as $product) {
            $productPage = Factory::getPageFactory()->getCatalogProductView();
            $productPage->init($product);
            $productPage->open();
            $productPage->getViewBlock()->addToCart($product);
        }

        //Proceed to checkout
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();

        //Complete checkout
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->getLoginBlock()->checkoutMethod($fixture);
        $checkoutOnePage->getBillingBlock()->fillBilling($fixture);
        $checkoutOnePage->getShippingMethodBlock()->selectShippingMethod($fixture);
        $checkoutOnePage->getPaymentMethodsBlock()->selectPaymentMethod($fixture);
        $checkoutOnePage->getReviewBlock()->placeOrder();

        $checkoutOnePageSuccess = Factory::getPageFactory()->getCheckoutOnepageSuccess();
        return $checkoutOnePageSuccess->isSuccessBlockVisible()
            ? $checkoutOnePageSuccess->getSuccessBlock()->getOrderId($fixture)
            : false;
    }
}
