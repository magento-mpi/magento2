<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerBalance\Test\TestCase;

use Mtf\Client\Browser;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Checkout\Test\Page\CheckoutOnepage;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Fixture\AddressInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Test Creation for DeleteStoreCreditFromCurrentQuote
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Enable free shipping in configuration
 * 2. Create simple product
 * 3. Create customer
 * 4. Add 100 Store Credit on customer balance
 *
 * Steps:
 * 1. Login to frontend as customer
 * 2. Add simple product to shopping cart
 * 3. Click "Proceed Checkout" button
 * 4. On Billing Information tab fill address and click "Continue" button
 * 5. On Shipping Method tab select "Free Shipping" by default and click "Continue"
 * 6. On Payment Information tab select "Use Store Credit" checkbox and click "Continue"
 * 7. On Order Review tab click "Remove" link(Remove Store Credit)
 * 8. Perform all asserts
 *
 * @group Customers_(CS)
 * @ZephyrId MAGETWO-27688
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DeleteStoreCreditFromCurrentQuoteTest extends Injectable
{
    /**
     * CmsIndex Page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Customer login page
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * CatalogProductView Page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * CheckoutCart Page
     *
     * @var CheckoutCart
     */
    protected $checkoutCart;

    /**
     * CheckoutOnepage Page
     *
     * @var CheckoutOnepage
     */
    protected $checkoutOnepage;

    /**
     * Fixture Factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Enable free shipping in configuration and create simple product
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $config = $fixtureFactory->createByCode('configData', ['dataSet' => 'freeshipping']);
        $config->persist();

        $product = $fixtureFactory->createByCode('catalogProductSimple');
        $product->persist();

        return ['product' => $product];
    }

    /**
     * Injection data
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param CheckoutOnepage $checkoutOnepage
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        CheckoutOnepage $checkoutOnepage,
        FixtureFactory $fixtureFactory
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->catalogProductView = $catalogProductView;
        $this->checkoutCart = $checkoutCart;
        $this->checkoutOnepage = $checkoutOnepage;
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Delete Store Credit from current quote
     *
     * @param CustomerInjectable $customer
     * @param Browser $browser
     * @param CatalogProductSimple $product
     * @param AddressInjectable $billingAddress
     * @param array $shipping
     * @param array $payment
     * @return void
     */
    public function test(
        CustomerInjectable $customer,
        Browser $browser,
        CatalogProductSimple $product,
        AddressInjectable $billingAddress,
        array $shipping,
        array $payment
    ) {
        // Precondition
        $customer->persist();
        $customerBalance = $this->fixtureFactory->createByCode(
            'customerBalance',
            [
                'dataSet' => 'customerBalance_100',
                'data' => [
                    'customer_id' => ['customer' => $customer],
                ]
            ]
        );
        $customerBalance->persist();

        // Steps
        $this->cmsIndex->open();
        $this->cmsIndex->getLinksBlock()->openLink("Log In");
        $this->customerAccountLogin->getLoginBlock()->login($customer);
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $this->catalogProductView->getViewBlock()->clickAddToCartButton();
        $this->checkoutCart->getCartBlock()->getOnepageLinkBlock()->proceedToCheckout();
        $this->checkoutOnepage->getBillingBlock()->fillBilling($billingAddress);
        $this->checkoutOnepage->getBillingBlock()->clickContinue();
        $this->checkoutOnepage->getShippingMethodBlock()->selectShippingMethod($shipping);
        $this->checkoutOnepage->getShippingMethodBlock()->clickContinue();
        $this->checkoutOnepage->getStoreCreditBlock()->fillStoreCredit($payment);
        $this->checkoutOnepage->getPaymentMethodsBlock()->selectPaymentMethod($payment);
        $this->checkoutOnepage->getPaymentMethodsBlock()->clickContinue();
        $this->checkoutOnepage->getBalanceReviewBlock()->clickRemoveStoreCredit();
    }
}
