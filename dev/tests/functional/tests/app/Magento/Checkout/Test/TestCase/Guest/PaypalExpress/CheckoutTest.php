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
use Mtf\Util\Generate\FixtureFactory;

class CheckoutTest extends Functional
{

    /**
     * @var \Magento\Paypal\Test\Fixture\Customer
     */
    protected $_customerFixture;

    /**
     * @var \Magento\Customer\Test\Fixture\Address
     */
    protected $_customerAddressFixture;

    /**
     * @var \Magento\Catalog\Test\Fixture\Product
     */
    protected $_productFixture;

    /**
     * @var \Magento\Shipping\Test\Fixture\Method
     */
    protected $_shippingMethodFixture;

    /**
     * @var \Magento\Shipping\Test\Fixture\Method
     */
    protected $_configFixture;

    /**
     * @var \Magento\Checkout\Test\Fixture\GuestPaypalExpress
     */
    protected $_paypalExpressFixture;

    /**
     *  Set up test
     */
    protected function setUp()
    {
        $this->_customerFixture         = Factory::getFixtureFactory()->getMagentoPaypalCustomer();
        $this->_productFixture          = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $this->_customerAddressFixture  = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $this->_shippingMethodFixture   = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $this->_configFixture           = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $this->_paypalExpressFixture    = Factory::getFixtureFactory()->getMagentoCheckoutGuestPaypalExpress();
    }

    /**
     *  Tear down test
     */
    protected function tearDown()
    {
        $this->_customerFixture         = null;
        $this->_productFixture          = null;
        $this->_customerAddressFixture  = null;
        $this->_shippingMethodFixture   = null;
        $this->_configFixture           = null;
        $this->_paypalExpressFixture    = null;
    }

    /**
     * Prepare config for test
     */
    protected function _prepareConfig()
    {
//        if ($this->_configFixture->switchData('free_shipping')) {
//            $this->_configFixture->persist();
//        }
//        if ($this->_configFixture->switchData('paypal_express')) {
//            $this->_configFixture->persist();
//        }

//        $coreConfig->switchData('paypal_disabled_all_methods');
//        $coreConfig->persist();
//
//        $coreConfig->switchData('paypal_express');
//        $coreConfig->persist();
//
//        $coreConfig->switchData('default_tax_config');
//        $coreConfig->persist();

    }
    /**
     *  Process product view page
     */
    protected function _processProductViewPage()
    {
        if ($this->_productFixture->switchData('simple')) {
            $this->_productFixture->persist();
        }
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->init($this->_productFixture);
        $productPage->open();
        $productPage->getViewBlock()->paypalCheckout();
    }

    /**
     * Process Paypal checkout page
     */
    protected function _processPaypalCheckoutPage()
    {
        $this->_customerFixture->switchData('customer_US');
        $paypalPage = Factory::getPageFactory()->getPaypal();
        $paypalPage->getLoginBlock()->login($this->_customerFixture);
        $paypalPage->getReviewBlock()->continueCheckout();
    }

    /**
     * Process verify order page
     */
    protected function _processVerifyOrderPage()
    {
        $this->_shippingMethodFixture->switchData('free_shipping');
        $checkoutReviewPage = Factory::getPageFactory()->getPaypalExpressReview();
        $checkoutReviewPage->getReviewBlock()->fillTelephone($this->_customerAddressFixture);
        $checkoutReviewPage->getReviewBlock()->selectShippingMethod($this->_shippingMethodFixture);
        $checkoutReviewPage->getReviewBlock()->placeOrder();
    }

    /**
     * Process actions on orders page
     */
    protected function _processAdminOrderPage()
    {
        $orderId = Factory::getPageFactory()->getCheckoutOnepageSuccess()->getSuccessBlock()->getGuestOrderId();
        Factory::getApp()->magentoBackendLoginUser();
        $orderPage = Factory::getPageFactory()->getAdminSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
        $expected = $this->_paypalExpressFixture->getData('totals/grand_total');
        $actual = Factory::getPageFactory()->getAdminSalesOrderView()->getOrderTotalsBlock()->getGrandTotal();

        $this->assertContains($expected, $actual, 'Incorrect grand total value for the order #' . $orderId);
        $this->assertContains($expected, $actual, 'Incorrect grand total value for the order #' . $orderId);
    }

    /**
     * Guest checkout using "Checkout with PayPal" button from product page and Free Shipping
     */
    public function testCheckoutFreeShipping()
    {
        $this->_prepareConfig();
        $this->_processProductViewPage();
        $this->_processPaypalCheckoutPage();
        $this->_processVerifyOrderPage();
        $this->_processAdminOrderPage();
    }
}
