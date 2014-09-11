<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\TestCase;

use Mtf\Client\Browser;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Checkout\Test\Page\CheckoutOnepage;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Fixture\AddressInjectable;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Reward\Test\Page\Adminhtml\RewardRateNew;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Reward\Test\Page\Adminhtml\RewardRateIndex;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Test Creation for Remove Reward Points On Checkout
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create Simple Product
 * 2. Setup reward points rates
 * 3. Create default customer
 * 4. Add reward points to customer according to dataSet
 *
 * Steps:
 * 1. Login to Frontend
 * 2. Add product to cart and proceed checkout
 * 3. Fill billing address
 * 4. Use checkbox "Shipping same as Billing"
 * 5. Choose Flat Rate
 * 6. On payment section Select Use Reward Points
 * 7. On Order Review page click Remove reward points
 * 8. Perform all asserts
 *
 * @group Reward_Points_(CS)
 * @ZephyrId MAGETWO-28074
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RemoveRewardPointsOnCheckoutTest extends Injectable
{
    /**
     * Factory for fixture
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

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
     * Customer logout page
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

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
     * Reward rate index page
     *
     * @var RewardRateIndex
     */
    protected static $rewardRateIndexPage;

    /**
     * Reward rate new page
     *
     * @var RewardRateNew
     */
    protected static $rewardRateNewPage;

    /**
     * Create simple product and setup reward rates
     *
     * @param RewardRateIndex $rewardRateIndexPage
     * @param RewardRateNew $rewardRateNewPage
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(
        RewardRateIndex $rewardRateIndexPage,
        RewardRateNew $rewardRateNewPage,
        FixtureFactory $fixtureFactory
    ) {
        self::$rewardRateIndexPage = $rewardRateIndexPage;
        self::$rewardRateNewPage = $rewardRateNewPage;
        $this->fixtureFactory = $fixtureFactory;
        $product = $fixtureFactory->createByCode('catalogProductSimple');
        $product->persist();
        $rewardRate = $fixtureFactory->createByCode('rewardRate', ['dataSet' => 'rate_1_point_to_1_currency']);
        $rewardRate->persist();
        $rewardRate = $fixtureFactory->createByCode('rewardRate', ['dataSet' => 'rate_1_currency_to_1_point']);
        $rewardRate->persist();

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
     * @param CustomerAccountLogout $customerAccountLogout
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        CheckoutOnepage $checkoutOnepage,
        CustomerAccountLogout $customerAccountLogout
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->catalogProductView = $catalogProductView;
        $this->checkoutCart = $checkoutCart;
        $this->checkoutOnepage = $checkoutOnepage;
        $this->customerAccountLogout = $customerAccountLogout;
    }

    /**
     * Removing Reward Points On Checkout
     *
     * @param CustomerInjectable $customer
     * @param CatalogProductSimple $product
     * @param Browser $browser
     * @param AddressInjectable $billingAddress
     * @param array $shipping
     * @param array $payment
     * @param string $rewardPoints
     * @return void
     */
    public function test(
        CustomerInjectable $customer,
        CatalogProductSimple $product,
        Browser $browser,
        AddressInjectable $billingAddress,
        array $shipping,
        array $payment,
        $rewardPoints
    ) {
        // Preconditions
        $customer->persist();
        $reward = $this->fixtureFactory->createByCode(
            'reward',
            [
                'dataSet' => $rewardPoints,
                'data' => [
                    'customer_id' => ['customer' => $customer],
                ]
            ]
        );
        $reward->persist();

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
        $this->checkoutOnepage->getRewardPointsBlock()->fillReward($payment);
        $this->checkoutOnepage->getPaymentMethodsBlock()->selectPaymentMethod($payment);
        $this->checkoutOnepage->getPaymentMethodsBlock()->clickContinue();
        $this->checkoutOnepage->getRewardReviewBlock()->clickRemoveRewardPoints();
    }

    /**
     * Logout customer
     *
     * @return void
     */
    public function tearDown()
    {
        $this->customerAccountLogout->open();
    }

    /**
     * Delete reward exchange rates
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        self::$rewardRateIndexPage->open();
        while (self::$rewardRateIndexPage->getRewardRateGrid()->isFirstRowVisible()) {
            self::$rewardRateIndexPage->getRewardRateGrid()->openFirstRow();
            self::$rewardRateNewPage->getFormPageActions()->delete();
        }
    }
}
