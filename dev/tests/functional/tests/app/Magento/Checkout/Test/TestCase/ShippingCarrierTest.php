<?php
/**
 * {license_notice}
 *
 * @category Mtf
 * @package Mtf
 * @subpackage functional_tests
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\Checkout\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Checkout\Test\Fixture\Checkout;
use Magento\Customer\Test\Fixture\Customer;
use Magento\Core\Test\Fixture\Config;


/**
 * Class ShippingCarrierTest
 * Test checking out with each of the shipping carriers
 *
 * @package Magento\Test\TestCase
 */
class ShippingCarrierTest extends Functional
{
    /**
     * Check money order checkout fixture
     *
     * @var Checkout $checkoutFixture
     */
    protected static $checkoutFixture;

    /**
     * Store configuration fixture
     *
     * @var Config $configFixture
     */
    protected static $configFixture;

    /**
     * Create and persist checkout fixture
     */
    //protected function setUp()
    public static function setUpBeforeClass()
    {
        // Use checkout fixture to setup precondition data that will be shared by each data-set in the data provider
        // Create simple, configurable, and bundled products
        // Check or money order
        self::$checkoutFixture = Factory::getFixtureFactory()->getMagentoCheckoutCheckMoneyOrder();
        self::$checkoutFixture->persist();

        // Enable store configuration - Shipping Settings -> Origin for this test
        self::$configFixture = Factory::getFixtureFactory()->getMagentoCoreConfig();
        self::$configFixture->switchData('shipping_origin_us');
        self::$configFixture->persist();
    }

    /**
     * Place order on frontend using shipping carrier from data provider.
     *
     * @param $shippingMethodConfig
     * @param $shippingMethodCheckout
     * @param $customerDataSet
     * @param $addressDataSet
     * @dataProvider dataProviderShippingCarriers
     * @ZephyrId MAGETWO-12844
     * @ZephyrId MAGETWO-12848
     */
    public function testShippingCarriers(
        $shippingMethodConfig, $shippingMethodCheckout, $customerDataSet, $addressDataSet)
    {
        // Enable shipping method in store configuration based on method specified in data provider
        self::$configFixture->switchData($shippingMethodConfig);
        self::$configFixture->persist();

        // Declare shipping methods based on what will be selected at checkout
        $shippingMethods = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $shippingMethods->switchData($shippingMethodCheckout);

        // Create customer based upon data-provider data-set
        $customer = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $customer->switchData($customerDataSet);
        $customer->persist();
        self::$checkoutFixture->setCustomer($customer);

        // Specify existing customer data-set (does not contain email address or password)
        $billingAddress = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $billingAddress->switchData($addressDataSet);
        self::$checkoutFixture->setBillingAddress($billingAddress);

        // Frontend
        // Ensure shopping cart is empty
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();
        // Ensure customer is logged out
        $customerAccountLogoutPage = Factory::getPageFactory()->getCustomerAccountLogout();
        $customerAccountLogoutPage->open();

        // Frontend
        // Add simple, configurable, and bundle products to cart
        foreach (self::$checkoutFixture->getProducts() as $product) {
            $productPage = Factory::getPageFactory()->getCatalogProductView();
            $productPage->init($product);
            $productPage->open();
            $productPage->getViewBlock()->addToCart($product);
            Factory::getPageFactory()->getCheckoutCart()->getMessageBlock()->assertSuccessMessage();
        }

        $cartShippingBlock = Factory::getPageFactory()->getCheckoutCart()->getEstimatedShippingBlock();
        // Make estimated shipping content visible
        $cartShippingBlock->clickEstimateShippingTax();
        $cartShippingBlock->fillDestination(self::$checkoutFixture);
        $cartShippingBlock->clickGetAQuote();
        $cartShippingRateBlock = Factory::getPageFactory()->getCheckoutCart()->getEstimatedShippingRateBlock();
        $cartShippingRateBlock->assertShippingCarrierMethod($shippingMethods);

        // Login with customer created in checkout fixture
        $customerAccountLoginPage = Factory::getPageFactory()->getCustomerAccountLogin();
        $customerAccountLoginPage->open();
        $customerAccountLoginPage->getLoginBlock()->login($customer);

        // Place order on frontend via onepage checkout
        // Use customer from checkout fixture
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->open();
        $checkoutOnePage->getBillingBlock()->fillBilling(self::$checkoutFixture);

        // Select shipping method at checkout based on method specified in data provider
        $checkoutOnePage->getShippingMethodBlock()->selectShippingMethod($shippingMethods);

        $checkoutOnePage->getPaymentMethodsBlock()->selectPaymentMethod(self::$checkoutFixture);
        $checkoutOnePage->getReviewBlock()->placeOrder();

        $successPage = Factory::getPageFactory()->getCheckoutOnepageSuccess();
        $orderId = $successPage->getSuccessBlock()->getOrderId(self::$checkoutFixture);

        // Verify order is present on backend (Sales-Orders)
        Factory::getApp()->magentoBackendLoginUser();
        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $orderPage->open();
        $this->assertTrue(
            $orderPage->getOrderGridBlock()->isRowVisible(array('id' => $orderId)),
            "Order # $orderId was not found on the sales orders grid!"
        );

        // Perform clean up between each data-set data provider run
        $this->cleanup();
    }

    /**
     * Data provider contains shipping method used for store configuration and frontend checkout.
     *
     * @return array
     */
    public function dataProviderShippingCarriers()
    {
        return array(
            array('shipping_carrier_usps', 'usps', 'customer_US_1', 'address_data_US_1'),
            array('shipping_carrier_ups', 'ups', 'customer_US_1', 'address_data_US_1')
        );
    }

    private function cleanup()
    {
        // Disable all shipping carriers
        self::$configFixture->switchData('shipping_disable_all_carriers');
        self::$configFixture->persist();

        // Set shipping settings origin back to US
        self::$configFixture->switchData('shipping_origin_us');
        self::$configFixture->persist();
    }
}
