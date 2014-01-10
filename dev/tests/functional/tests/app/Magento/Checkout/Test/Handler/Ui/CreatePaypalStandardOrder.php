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
 * Class CreatePaypalStandardOrder
 * Create an order using PayPal Payments Standard method
 *
 * @package Magento\Checkout\Test\Handler\Ui
 */
class CreatePaypalStandardOrder extends Ui
{
    /**
     * Create product
     *
     * @param Fixture $fixture [oiptional]
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

        //PayPal Site
        $paypalCustomer = $fixture->getPaypalCustomer();
        $paypalPage = Factory::getPageFactory()->getPaypal();
        $paypalPage->getBillingBlock()->clickLoginLink();
        $paypalPage->getLoginBlock()->login($paypalCustomer);
        $paypalPage->getReviewBlock()->continueCheckout();
        $paypalPage->getMainPanelBlock()->clickReturnLink();

        $successPage = Factory::getPageFactory()->getCheckoutOnepageSuccess();
        $orderId = $successPage->getSuccessBlock()->getOrderId($fixture);

        return $orderId;
    }
}
