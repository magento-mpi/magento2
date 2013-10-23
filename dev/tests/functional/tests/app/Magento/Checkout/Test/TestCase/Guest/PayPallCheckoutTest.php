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
use Mtf\Util\Generate\FixtureFactory;

class PayPallCheckoutTest extends Functional
{
    /**
     *  Process product view page
     */
    protected function _processProductViewPage()
    {
        $productFixture = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        if ($productFixture->switchData('simple')) {
            $productFixture->persist();
        }
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->init($productFixture);
        $productPage->open();
        $productPage->getViewBlock()->paypalCheckout();
    }

    /**
     * Process Paypal checkout page
     */
    protected function _processPaypalCheckoutPage()
    {
        $paypalCustomer = Factory::getFixtureFactory()->getMagentoPaypalCustomer();
        $paypalCustomer->switchData('customer_US');
        $paypalPage = Factory::getPageFactory()->getPaypal();
        $paypalPage->getLoginBlock()->login($paypalCustomer);
        $paypalPage->getReviewBlock()->continueCheckout();
    }

    /**
     * Process verify order page
     */
    protected function _processVerifyOrderPage()
    {
        $shippingMethodFixture = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $shippingMethodFixture->switchData('free_shipping');
        $addressFixture = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $checkoutReviewPage = Factory::getPageFactory()->getPaypalExpressReview();
        $checkoutReviewPage->getReviewBlock()->fillTelephone($addressFixture);
        $checkoutReviewPage->getReviewBlock()->selectShippingMethod($shippingMethodFixture);
        $checkoutReviewPage->getReviewBlock()->placeOrder();
    }

    /**
     * Guest checkout using "Checkout with PayPal" button from product page and Free Shipping
     */
    public function testCheckoutFreeShipping()
    {
        $this->_processProductViewPage();
        $this->_processPaypalCheckoutPage();
        $this->_processVerifyOrderPage();
    }
}
