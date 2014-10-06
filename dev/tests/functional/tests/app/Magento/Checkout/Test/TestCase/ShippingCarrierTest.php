<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\Checkout\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class ShippingCarrierTest
 * Test checking out with each of the shipping carriers
 *
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
     * Create and persist checkout fixture
     */
    public static function setUpBeforeClass()
    {
        // Use checkout fixture to setup precondition data that will be shared by each data-set in the data provider
        // Create simple, configurable, and bundled products
        // Check or money order
        self::$checkoutFixture = Factory::getFixtureFactory()->getMagentoCheckoutCheckMoneyOrder();
        self::$checkoutFixture->persist();
    }

    /**
     * Reset store configuration settings back to what other tests expect
     */
    public static function tearDownAfterClass()
    {
        self::initConfiguration();
    }

    /**
     * Place order on frontend using shipping carrier from data provider.
     *
     * @param string $shippingMethodConfig
     * @param string $shippingMethodCheckout
     * @param string $customerDataSet
     * @param string $addressDataSet
     * @param string|null $currencyRateDataSet
     *
     * @dataProvider dataProviderShippingCarriers
     * @ZephyrId MAGETWO-12444
     * @ZephyrId MAGETWO-12848
     * @ZephyrId MAGETWO-12849
     * @ZephyrId MAGETWO-12850
     * @ZephyrId MAGETWO-12851
     */
    public function testShippingCarriers(
        $shippingMethodConfig,
        $shippingMethodCheckout,
        $customerDataSet,
        $addressDataSet,
        $currencyRateDataSet = null
    ) {
        $this->performPreConditions(
            $shippingMethodConfig,
            $shippingMethodCheckout,
            $customerDataSet,
            $addressDataSet,
            $currencyRateDataSet
        );

        // Frontend
        // Ensure shopping cart is empty
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCartIndex();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();
        // Ensure customer from previous run is logged out
        $customerAccountLogoutPage = Factory::getPageFactory()->getCustomerAccountLogout();
        $customerAccountLogoutPage->open();
        // Login with customer created in checkout fixture
        $customerAccountLoginPage = Factory::getPageFactory()->getCustomerAccountLogin();
        $customerAccountLoginPage->open();
        $customerAccountLoginPage->getLoginBlock()->login(self::$checkoutFixture->getCustomer());
        // Add address to customer's address book to be used at checkout
        $accountIndexPage = Factory::getPageFactory()->getCustomerAccountIndex();
        $accountIndexPage->getDashboardAddress()->editBillingAddress();
        $addressEditPage = Factory::getPageFactory()->getCustomerAddressEdit();
        $addressEditPage->getEditForm()->editCustomerAddress((self::$checkoutFixture->getBillingAddress()));

        // Add simple, configurable, and bundle products to cart
        foreach (self::$checkoutFixture->getProducts() as $product) {
            $productPage = Factory::getPageFactory()->getCatalogProductView();
            Factory::getClientBrowser()->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
            $productPage->getViewBlock()->addToCart($product);
            Factory::getPageFactory()->getCheckoutCartIndex()->getMessagesBlock()->waitSuccessMessage();
        }

        // Get and verify shipping quote
        $cartPage = Factory::getPageFactory()->getCheckoutCartIndex();
        $cartShippingBlock = $cartPage->getShippingBlock();
        // Make estimated shipping content visible
        $cartShippingBlock->openEstimateShippingAndTax();
        $cartShippingBlock->fill(self::$checkoutFixture->getBillingAddress());
        $cartShippingBlock->clickGetQuote();
        // Verify expected shipping carrier and method are present
        $shippingMethod = self::$checkoutFixture->getShippingMethods()->getData('fields');
        $carrier = $shippingMethod['shipping_service'];
        $method = $shippingMethod['shipping_method'];
        $this->assertTrue(
            $cartPage->getShippingBlock()->isShippingCarrierMethodVisible($carrier, $method),
            "Shipping rate not found for $carrier - $method"
        );

        // Place order on frontend via onepage checkout
        // Use customer from checkout fixture
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->open();
        $checkoutOnePage->getBillingBlock()->clickContinue();

        // Select shipping method at checkout based on method specified in data provider
        $checkoutOnePage->getShippingMethodBlock()
            ->selectShippingMethod(self::$checkoutFixture->getShippingMethods()->getData('fields'));
        $checkoutOnePage->getShippingMethodBlock()->clickContinue();

        $payment = [
            'method' => self::$checkoutFixture->getPaymentMethod()->getPaymentCode(),
            'dataConfig' => self::$checkoutFixture->getPaymentMethod()->getDataConfig(),
            'credit_card' => self::$checkoutFixture->getCreditCard(),
        ];
        $checkoutOnePage->getPaymentMethodsBlock()->selectPaymentMethod($payment);
        $checkoutOnePage->getPaymentMethodsBlock()->clickContinue();
        $checkoutOnePage->getReviewBlock()->placeOrder();

        $successPage = Factory::getPageFactory()->getCheckoutOnepageSuccess();
        $orderId = $successPage->getSuccessBlock()->getOrderId(self::$checkoutFixture);

        // Verify order is present on backend (Sales-Orders)
        Factory::getApp()->magentoBackendLoginUser();
        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $orderPage->open();
        $this->assertTrue(
            $orderPage->getOrderGridBlock()->isRowVisible(['id' => $orderId]),
            "Order # $orderId was not found on the sales orders grid!"
        );
    }

    /**
     * Data provider contains shipping method used for store configuration and frontend checkout.
     *
     * @return array
     */
    public function dataProviderShippingCarriers()
    {
        return [
            ['shipping_carrier_usps', 'usps', 'customer_US_1', 'address_data_US_1'],
            ['shipping_carrier_ups', 'ups', 'customer_US_1', 'address_data_US_1'],
            ['shipping_carrier_fedex', 'fedex', 'customer_US_1', 'address_data_US_1'],
            ['shipping_carrier_dhl_eu', 'dhl_eu', 'customer_DE_1', 'address_DE', 'usd_chf_rate_0_9'],
            ['shipping_carrier_dhl_uk', 'dhl_uk', 'customer_UK_1', 'address_UK_2', 'usd_gbp_rate_0_6']
        ];
    }

    /**
     * This method initializes necessary store configuration settings in between data-provider runs.
     */
    private static function initConfiguration()
    {
        $configFixture = Factory::getFixtureFactory()->getMagentoCoreConfig();
        // Disable all shipping carriers
        $configFixture->switchData('shipping_disable_all_carriers');
        $configFixture->persist();

        // Sales > Shipping Settings > Origin - start with US address
        $configFixture->switchData('shipping_origin_us');
        $configFixture->persist();

        // General > Currency Setup > Currency Options - start with US Dollar
        $configFixture->switchData('currency_usd');
        $configFixture->persist();
    }

    /**
     * This method performs all preConditions for this data-set run.
     *
     * @param string $shippingMethodConfig
     * @param string $shippingMethodCheckout
     * @param string $customerDataSet
     * @param string $addressDataSet
     * @param string|null $currencyDataSet
     */
    private function performPreConditions(
        $shippingMethodConfig,
        $shippingMethodCheckout,
        $customerDataSet,
        $addressDataSet,
        $currencyDataSet
    ) {
        // Initialize store configuration for this data provider run
        $this->initConfiguration();

        // Configure shipping origin / shipping carrier
        // Enable shipping method in store configuration based on method specified in data provider
        $configFixture = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $configFixture->switchData($shippingMethodConfig);
        $configFixture->persist();

        // Configure currency rates if this data provider run requires it
        if (!is_null($currencyDataSet)) {
            $currencyRateFixture = Factory::getFixtureFactory()->getMagentoCurrencySymbolCurrencyRate();
            $currencyRateFixture->switchData($currencyDataSet);
            $currencyRateFixture->persist();
        }

        // Declare shipping methods based on what will be selected at checkout
        $shippingMethods = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $shippingMethods->switchData($shippingMethodCheckout);
        self::$checkoutFixture->setShippingMethod($shippingMethods);

        // Create customer based upon data-provider data-set
        $customer = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $customer->switchData($customerDataSet);
        $customer->persist();
        self::$checkoutFixture->setCustomer($customer);

        // Specify existing customer data-set (does not contain email address or password)
        $billingAddress = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $billingAddress->switchData($addressDataSet);
        self::$checkoutFixture->setBillingAddress($billingAddress);
    }
}
