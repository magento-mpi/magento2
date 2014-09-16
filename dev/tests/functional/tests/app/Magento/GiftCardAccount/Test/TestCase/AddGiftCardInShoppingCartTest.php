<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\TestCase;

use Mtf\Client\Browser;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Test Creation for AddGiftCardInShoppingCartTest
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create Customer
 * 2. Create GiftCard account
 *
 * Steps:
 * 1. Go to frontend
 * 2. Login as a Customer if Customer Name is specified in Data Set
 * 3. Add Product (according to dataset) to the Cart
 * 4. Expand Gift Cards tab and fill code
 * 5. Click Add Gift Card
 * 6. Perform appropriate assertions
 *
 * @group Mini_Shopping_Cart_(CS)
 * @ZephyrId MAGETWO-28388
 */
class AddGiftCardInShoppingCartTest extends Injectable
{
    /**
     * Fixture Factory
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
     * CustomerAccountLogin Page on frontend
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
     * Customer account logout on frontend
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Create customer and gift card account
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $customer = $fixtureFactory->createByCode('customerInjectable', ['dataSet' => 'default']);
        $customer->persist();

        $giftCardAccount = $fixtureFactory->createByCode('giftCardAccount', ['dataSet' => 'active_redeemable_account']);
        $giftCardAccount->persist();

        return [
            'customerInjectable' => $customer,
            'giftCardAccount' => $giftCardAccount,
        ];
    }

    /**
     * Injection data
     *
     * @param FixtureFactory $fixtureFactory
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param CustomerAccountLogout $customerAccountLogout
     * @return void
     */
    public function __inject(
        FixtureFactory $fixtureFactory,
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        CustomerAccountLogout $customerAccountLogout
    ) {
        $this->fixtureFactory = $fixtureFactory;
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->catalogProductView = $catalogProductView;
        $this->checkoutCart = $checkoutCart;
        $this->customerAccountLogout = $customerAccountLogout;
    }

    /**
     * Add GiftCard in ShoppingCart
     *
     * @param CustomerInjectable $customerInjectable
     * @param GiftCardAccount $giftCardAccount
     * @param Browser $browser
     * @param string $product
     * @param $customer
     * @return array
     */
    public function test(
        CustomerInjectable $customerInjectable,
        GiftCardAccount $giftCardAccount,
        Browser $browser,
        $product,
        $customer
    ) {
        // Preconditions
        list($fixture, $dataSet) = explode('::', $product);
        $product = $this->fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
        $product->persist();

        // Steps
        $this->cmsIndex->open();
        if ($customer !== '-') {
            $this->cmsIndex->getLinksBlock()->openLink("Log In");
            $this->customerAccountLogin->getLoginBlock()->login($customerInjectable);
        }
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $this->catalogProductView->getViewBlock()->addToCart($product);
        $this->checkoutCart->getGiftCardAccountBlock()->addGiftCard($giftCardAccount->getCode());

        return ['code' => $giftCardAccount->getCode()];
    }

    /**
     * Logout customer from frontend account
     *
     * @return void
     */
    public function tearDown()
    {
        $this->customerAccountLogout->open();
    }
}
