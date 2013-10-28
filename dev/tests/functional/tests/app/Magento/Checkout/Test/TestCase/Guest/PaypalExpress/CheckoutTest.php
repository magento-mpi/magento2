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
use Magento\Checkout\Test\Fixture\PaypalExpressGuestCheckout;

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
     * @var \Magento\Core\Test\Fixture\Config
     */
    protected $_configFixture;

    /**
     * @var \Magento\Checkout\Test\Fixture\PaypalExpressGuestCheckout
     */
    protected $_paypalExpressFixture;

    /**
     * @var \Magento\Tax\Test\Fixture\TaxClass
     */
    protected $_taxClassFixture;

    /**
     * @var \Magento\Tax\Test\Fixture\TaxRate
     */
    protected $_taxRateFixture;

    /**
     * @var \Magento\Tax\Test\Fixture\TaxRule
     */
    protected $_taxRuleFixture;

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
        $this->_paypalExpressFixture    = Factory::getFixtureFactory()->getMagentoCheckoutPaypalExpressGuestCheckout();
        $this->_taxClassFixture         = Factory::getFixtureFactory()->getMagentoTaxTaxClass();
        $this->_taxRateFixture          = Factory::getFixtureFactory()->getMagentoTaxTaxRate();
        $this->_taxRuleFixture          = Factory::getFixtureFactory()->getMagentoTaxTaxRule();
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
        $this->_taxClassFixture         = null;
        $this->_taxRateFixture          = null;
        $this->_taxRuleFixture          = null;
    }

    /**
     * Prepare config for test
     */
    protected function _prepareConfig()
    {
        $this->_configFixture->switchData('paypal_disabled_all_methods');
        $this->_configFixture->persist();
        $this->_configFixture->switchData('free_shipping');
        $this->_configFixture->persist();
        $this->_configFixture->switchData('paypal_express');
        $this->_configFixture->persist();
        $this->_configFixture->switchData('us_tax_config');
        $this->_configFixture->persist();
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

    protected function _processTaxes()
    {
        $this->_taxClassFixture->persist();
        $this->_taxRateFixture->persist();
        $this->_taxRuleFixture->persist();
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
        $expectedGrandTotal = $this->_paypalExpressFixture->getData('totals/grand_total');
        $actualGrandTotal = Factory::getPageFactory()->getAdminSalesOrderView()->getOrderTotalsBlock()->getGrandTotal();
        $expectedAuthorizedAmount = $this->_paypalExpressFixture->getData('totals/authorized_amount');
        $actualAuthorizedAmount = Factory::getPageFactory()->getAdminSalesOrderView()->getOrderHistoryBlock()->getAuthorizedAmount();
        $expectedCommentHistory = $this->_paypalExpressFixture->getData('totals/comment_history');
        $actualCommentHistory = Factory::getPageFactory()->getAdminSalesOrderView()->getOrderHistoryBlock()->getCommentHistory();
        $this->assertContains($expectedGrandTotal, $actualGrandTotal, 'Incorrect grand total value for the order #' . $orderId);
        $this->assertContains($expectedAuthorizedAmount, $actualAuthorizedAmount, 'Incorrect authorized amount value for the order #' . $orderId);
    }

    /**
     * Guest checkout using "Checkout with PayPal" button from product page and Free Shipping
     */
    public function testCheckoutFreeShipping()
    {
        $this->_prepareConfig();
        $this->_processTaxes();
        $this->_processProductViewPage();
        $this->_processPaypalCheckoutPage();
        $this->_processVerifyOrderPage();
        $this->_processAdminOrderPage();

    }
}
