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
     * Create and persist checkout fixture
     */
    protected function setUp()
    {
        $this->checkoutFixture = Factory::getFixtureFactory()->getMagentoCheckoutExistingCustomer();
        $this->checkoutFixture->persist();
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
        $products = $this->checkoutFixture->getProducts();
        foreach ($products as $product) {
            $productPage = Factory::getPageFactory()->getCatalogProductView();
            $productPage->init($product);
            $productPage->open();
            $productPage->getViewBlock()->addToCart($product);
            Factory::getPageFactory()->getCheckoutCart()->getMessageBlock()->assertSuccessMessage();
        }

        // Enable shipping method in store configuration
        $configFixture = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $configFixture->switchData($shippingMethodConfig);
        $configFixture->persist();

        // Proceed to one page checkout
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();

        // Place order on frontend
        // Use customer from checkout fixture
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $checkoutOnePage->open();
        $checkoutOnePage->getBillingBlock()->fillBilling($this->checkoutFixture);

        // Select shipping method at checkout
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
        $configFixture->switchData('shipping_disable_all_carriers');
        $configFixture->persist();
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
