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

namespace Magento\Checkout\Test\TestCase\Guest;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Checkout\Test\Fixture\Checkout;
use Magento\Checkout\Test\Fixture\GuestPayPalPayflowPro3dSecure;

/**
 * Class CheckoutOnepage3dSecureTest
 * Tests checkout via Magento one page checkout and 3D Secure payment methods.
 *
 * @package Magento\Checkout
 */
class CheckoutOnepage3dSecureTest extends Functional
{
    /**
     * Place order on frontend via one page checkout and PayPal PayflowPro 3D Secure payment method.
     */
    public function testOnepageCheckoutPayPalPayflowPro()
    {
        //Data
        $fixture = Factory::getFixtureFactory()->getMagentoCheckoutGuestPayPalPayflowPro3dSecure();
        $fixture->persist();
        //Steps
        $this->_addProducts($fixture);
        $this->_magentoCheckoutProcess($fixture);
        $this->_validateAndPlaceOrder($fixture);
        //Verifying
        $this->_verifyOrder($fixture);
    }

    /**
     * Add products to cart
     *
     * @param Checkout $fixture
     */
    protected function _addProducts(Checkout $fixture)
    {
        //Ensure shopping cart is empty
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();

        $products = $fixture->getProducts();
        foreach ($products as $product) {
            $productPage = Factory::getPageFactory()->getCatalogProductView();
            $productPage->init($product);
            $productPage->open();
            $productPage->getViewBlock()->addToCart($product);
            Factory::getPageFactory()->getCheckoutCart()->getMessageBlock()->assertSuccessMessage();
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
        /** @var \Magento\Checkout\Test\Page\CheckoutOnepage $checkoutOnePage */
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->getLoginBlock()->checkoutMethod($fixture);
        $checkoutOnePage->getBillingBlock()->fillBilling($fixture);
        $checkoutOnePage->getShippingMethodBlock()->selectShippingMethod($fixture);
        $checkoutOnePage->getPaymentMethodsBlock()->selectPaymentMethod($fixture);
    }

    /**
     * Submit 3D Secure Verification form and place order
     */
    protected function _validateAndPlaceOrder(Checkout $fixture)
    {
        /** @var \Magento\Checkout\Test\Page\CheckoutOnepage $checkoutOnePage */
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        /** @var  \Magento\Centinel\Test\Block\Authentication $centinelBlock */
        $centinelBlock = $checkoutOnePage->getCentinelAuthenticationBlock();
        $centinelBlock->verifyCard($fixture);

        $checkoutOnePage->getReviewBlock()->waitForCardValidation();
        $checkoutOnePage->getReviewBlock()->placeOrder();
    }

    /**
     * Verify created order
     *
     * @param Checkout $fixture
     */
    protected function _verifyOrder(Checkout $fixture)
    {
        //Order placed
        $successPage = Factory::getPageFactory()->getCheckoutOnepageSuccess();
        $this->assertContains(
            'Your order has been received.',
            $successPage->getTitleBlock()->getTitle(),
            'Order success page was not opened.');
        $orderId = $successPage->getSuccessBlock()->getOrderId($fixture);

        //Check order data on backend
        Factory::getApp()->magentoBackendLoginUser();
        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));

        $this->assertContains(
            $fixture->getGrandTotal(),
            Factory::getPageFactory()->getSalesOrderView()->getOrderTotalsBlock()->getGrandTotal(),
            'Incorrect grand total value for the order #' . $orderId);

        $this->assertContains(
            $fixture->getVerificationResult(),
            Factory::getPageFactory()->getSalesOrderView()->getOrderInfoBlock()->getVerificationResult(),
            'Incorrect "3D Secure Verification Result" the order #' . $orderId);

        $this->assertContains(
            $fixture->getCardholderValidation(),
            Factory::getPageFactory()->getSalesOrderView()->getOrderInfoBlock()->getCardholderValidation(),
            'Incorrect "3D Secure Cardholder Validation" for the order #' . $orderId);

        $this->assertContains(
            $fixture->getEcommerceIndicator(),
            Factory::getPageFactory()->getSalesOrderView()->getOrderInfoBlock()->getEcommerceIndicator(),
            'Incorrect "3D Secure Electronic Commerce Indicator" for the order #' . $orderId);
    }
}
