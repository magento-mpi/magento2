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
     * Store configuration fixture
     *
     * @var Config
     */
    protected static $configFixture;

    /**
     * Array of products used during checkout.  Simple, configurable, and bundle product.
     *
     * @var \Magento\Catalog\Test\Fixture\Product[]
     */
    protected static $products = array();

    /**
     * Create and persist checkout fixture
     */
    //protected function setUp()
    public static function setUpBeforeClass()
    {
        // Setup precondition data that will be shared by each data-set in the data provider
        // Create simple, configurable, and bundled products
        $simple = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $simple->switchData('simple_required');
        $simple->persist();

        $configurable = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $configurable->switchData('configurable_required');
        $configurable->persist();

        $bundle = Factory::getFixtureFactory()->getMagentoBundleBundleFixed();
        $bundle->switchData('bundle_fixed_required');
        $bundle->persist();

        self::$products = array(
            $simple,
            $configurable,
            $bundle
        );

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
     * @param Checkout $checkoutFixture
     * @dataProvider dataProviderShippingCarriers
     * @ZephyrId MAGETWO-12848
     * @ZephyrId MAGETWO-12849
     */
    public function testShippingCarriers($shippingMethodConfig, $shippingMethodCheckout, Checkout $checkoutFixture)
    {
        // Create customer from passed in checkout fixture
        $checkoutFixture->persist();

        // Enable shipping method in store configuration based on method specified in data provider
        self::$configFixture->switchData($shippingMethodConfig);
        self::$configFixture->persist();

        // Declare shipping methods based on what will be selected at checkout
        $shippingMethods = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $shippingMethods->switchData($shippingMethodCheckout);

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
        foreach (self::$products as $product) {
            $productPage = Factory::getPageFactory()->getCatalogProductView();
            $productPage->init($product);
            $productPage->open();
            $productPage->getViewBlock()->addToCart($product);
            Factory::getPageFactory()->getCheckoutCart()->getMessageBlock()->assertSuccessMessage();
        }

        $cartShippingBlock = Factory::getPageFactory()->getCheckoutCart()->getEstimatedShippingBlock();
        // Make estimated shipping content visible
        $cartShippingBlock->clickEstimateShippingTax();
        $cartShippingBlock->fillDestination($checkoutFixture);
        $cartShippingBlock->clickGetAQuote();
        $cartShippingRateBlock = Factory::getPageFactory()->getCheckoutCart()->getEstimatedShippingRateBlock();
        $cartShippingRateBlock->assertShippingCarrierMethod($shippingMethods);

        // Login with customer created in checkout fixture
        $customerAccountLoginPage = Factory::getPageFactory()->getCustomerAccountLogin();
        $customerAccountLoginPage->open();
        $customerAccountLoginPage->getLoginBlock()->login($checkoutFixture->getCustomer());

        // Place order on frontend via onepage checkout
        // Use customer from checkout fixture
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->open();
        $checkoutOnePage->getBillingBlock()->fillBilling($checkoutFixture);

        // Select shipping method at checkout based on method specified in data provider
        $checkoutOnePage->getShippingMethodBlock()->selectShippingMethod($shippingMethods);

        $checkoutOnePage->getPaymentMethodsBlock()->selectPaymentMethod($checkoutFixture);
        $checkoutOnePage->getReviewBlock()->placeOrder();

        $successPage = Factory::getPageFactory()->getCheckoutOnepageSuccess();
        $orderId = $successPage->getSuccessBlock()->getOrderId($checkoutFixture);

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
            array('shipping_carrier_ups', 'ups',
                  Factory::getFixtureFactory()->getMagentoCheckoutCheckMoneyOrderCustomerUS())
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
