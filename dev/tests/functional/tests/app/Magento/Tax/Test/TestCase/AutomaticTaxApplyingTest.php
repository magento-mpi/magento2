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

namespace Magento\Tax\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Automatic tax applying
 */
class AutomaticTaxApplyingTest extends Functional
{
    /**
     * Checkout fixture for automatic tax applying scenario
     *
     * @var \Magento\Checkout\Test\Fixture\AutomaticTaxApplying
     */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = Factory::getFixtureFactory()->getMagentoCheckoutAutomaticTaxApplying();
        $this->fixture->persist();
    }

    /**
     * Automatic tax applying based on VAT ID
     *  - verify tax applying
     *  - verify customer group applying
     *
     * @ZephyrId MAGETWO-13436
     */
    public function testAutomaticTaxApplying()
    {
        // Pages
        $customerAccountLoginPage = Factory::getPageFactory()->getCustomerAccountLogin();
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutOnePage = Factory::getPageFactory()->getCheckoutOnepage();
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $successPage = Factory::getPageFactory()->getCheckoutOnepageSuccess();

        // Login with customer created in checkout fixture
        $customerAccountLoginPage->open();
        $customerAccountLoginPage->getLoginBlock()->login($this->fixture->getCustomer());

        // Ensure shopping cart is empty
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();

        // Add products to cart
        $productPage->init($this->fixture->getSimpleProduct());
        $productPage->open();
        $productPage->getViewBlock()->addToCart($this->fixture->getSimpleProduct());

        // Fill 'Estimate Shipping and Tax' section
        $checkoutCartPage->open();
        $shippingBlock = $checkoutCartPage->getShippingBlock();
        $shippingBlock->openEstimateShippingAndTax();
        $shippingBlock->fill($this->fixture->getCustomer()->getDefaultShippingAddress());
        $shippingBlock->getQuote();
        $this->verifyCartTotals();

        // Proceed Checkout
        $checkoutCartPage->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();
        $checkoutOnePage->getBillingBlock()->fillBilling($this->fixture);
        $checkoutOnePage->getShippingMethodBlock()->selectShippingMethod($this->fixture);
        $checkoutOnePage->getPaymentMethodsBlock()->selectPaymentMethod($this->fixture);
        $checkoutOnePage->getReviewBlock()->placeOrder();

        // Verify order in Backend
        $orderId = $successPage->getSuccessBlock()->getOrderId($this->fixture);
        $this->verifyOrderOnBackend($orderId, $this->fixture);
    }

    /**
     * Verify cart totals
     */
    protected function verifyCartTotals()
    {
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $totalsBlock = $checkoutCartPage->getTotalsBlock();
        $this->assertContains(
            $this->fixture->getCartTax(),
            $totalsBlock->getTax(),
            'Tax is not equal to expected value'
        );
        $this->assertContains(
            $this->fixture->getCartSubtotal(),
            $totalsBlock->getSubtotal(),
            'Subtotal is not equal to expected value'
        );
        $this->assertContains($this->fixture->getCartGrandTotal(),
            $totalsBlock->getGrandTotal(),
            'Gran Total is not equal to expected value'
        );
    }

    /**
     * Verifies order in Backend. Verify order grand total and customer group
     *
     * @param string $orderId
     */
    protected function verifyOrderOnBackend($orderId)
    {
        Factory::getApp()->magentoBackendLoginUser();
        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
        $orderViewPage = Factory::getPageFactory()->getSalesOrderView();

        $this->assertContains(
            $this->fixture->getGrandTotal(),
            $orderViewPage->getOrderTotalsBlock()->getGrandTotal(),
            'Order subtotal price is incorrect'
        );
        $this->assertEquals(
            $this->fixture->getValidVatIntraUnionGroup(),
            $orderViewPage->getInformationBlock()->getCustomerGroup(),
            'Customer group is not equal "Valid VAT ID - Intra-Union"'
        );
    }

    /**
     * Remove created customer groups and disable automatic group assign
     */
    protected function tearDown()
    {
        Factory::getApp()->magentoCustomerRemoveCustomerGroup($this->fixture);

        $config = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $config->switchData('customer_disable_group_assign');
        $config->persist();
    }
}
