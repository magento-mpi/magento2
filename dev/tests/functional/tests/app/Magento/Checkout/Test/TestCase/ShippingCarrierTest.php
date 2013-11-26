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
     * Checkout fixture
     *
     * @var Checkout
     */
    protected $checkoutFixture;

    /**
     * Store configuration fixture
     *
     * @var Config
     */
    protected $configFixture;

    /**
     * Array of products used during checkout.  Simple, configurable, and bundle product.
     *
     * @var \Magento\Catalog\Test\Fixture\Product[]
     */
    protected $products = array();

    /**
     * Create and persist checkout fixture
     */
    protected function setUp()
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

        $this->products = array(
            $simple,
            $configurable,
            $bundle
        );

        // Create customer via checkout fixture
        $this->checkoutFixture = Factory::getFixtureFactory()->getMagentoCheckoutExistingCustomerCheckMoneyOrder();
        $this->checkoutFixture->persist();

        // Enable store configuration - Shipping Settings -> Origin for this test
        $this->configFixture = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $this->configFixture->switchData('shipping_origin_us');
        $this->configFixture->persist();
    }

    /**
     * Place order on frontend using shipping carrier from data provider.
     *
     * @param string $shippingMethodConfig
     * @param string $shippingMethodCheckout
     * @dataProvider dataProviderShippingCarriers
     * @ZephyrId MAGETWO-12848
     */
    public function testShippingCarriers($shippingMethodConfig, $shippingMethodCheckout)
    {
        // Frontend
        // Ensure shopping cart is empty
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();

        // Login with customer created in checkout fixture
        $customerAccountLoginPage = Factory::getPageFactory()->getCustomerAccountLogin();
        $customerAccountLoginPage->open();
        $customerAccountLoginPage->getLoginBlock()->login($this->checkoutFixture->getCustomer());

        // Add simple, configurable, and bundle products to cart
        foreach ($this->products as $product) {
            $productPage = Factory::getPageFactory()->getCatalogProductView();
            $productPage->init($product);
            $productPage->open();
            $productPage->getViewBlock()->addToCart($product);
            Factory::getPageFactory()->getCheckoutCart()->getMessageBlock()->assertSuccessMessage();
        }

        // Enable shipping method in store configuration based on method specified in data provider
        $this->configFixture->switchData($shippingMethodConfig);
        $this->configFixture->persist();

        // Proceed to one page checkout
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();

        // Place order on frontend
        // Use customer from checkout fixture
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->open();
        $checkoutOnePage->getBillingBlock()->fillBilling($this->checkoutFixture);

        // Select shipping method at checkout based on method specified in data provider
        $shippingMethods = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $shippingMethods->switchData($shippingMethodCheckout);
        $checkoutOnePage->getShippingMethodBlock()->selectShippingMethod($shippingMethods);

        $checkoutOnePage->getPaymentMethodsBlock()->selectPaymentMethod($this->checkoutFixture);
        $checkoutOnePage->getReviewBlock()->placeOrder();

        $successPage = Factory::getPageFactory()->getCheckoutOnepageSuccess();
        $orderId = $successPage->getSuccessBlock()->getOrderId($this->checkoutFixture);

        // Verify order is present on backend (Sales-Orders)
        Factory::getApp()->magentoBackendLoginUser();
        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $orderPage->open();
        $this->assertTrue(
            $orderPage->getOrderGridBlock()->isRowVisible(array('id' => $orderId)),
            "Order # $orderId was not found on the sales orders grid!"
        );

        // Perform clean up
        // Disable all shipping carriers
        $this->configFixture->switchData('shipping_disable_all_carriers');
        $this->configFixture->persist();
    }

    /**
     * Data provider contains shipping method used for store configuration and frontend checkout.
     *
     * @return array
     */
    public function dataProviderShippingCarriers()
    {
        return array(
            array('shipping_carrier_ups', 'ups')
        );
    }
}
