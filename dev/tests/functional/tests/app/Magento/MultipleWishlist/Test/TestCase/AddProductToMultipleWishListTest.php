<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\TestCase;

use Mtf\Client\Browser;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\InjectableFixture;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;

/**
 * Test Creation for AddProductToMultipleWishList
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create Product
 * 2. Enable Multiple Wishlist functionality
 * 3. Create Customer Account
 * 4. Create Wishlist
 *
 * Steps:
 * 1. Login to frontend as a customer
 * 2. Navigate to created product
 * 3. Select created wishlist and add product to it
 * 4. Perform appropriate assertions.
 *
 * @group Multiple_Wishlists_(CS)
 * @ZephyrId MAGETWO-29044
 */
class AddProductToMultipleWishListTest extends Injectable
{
    /**
     * Cms index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Customer Account Login Page
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * Customer account logout page
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Catalog Product View Page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Browser object
     *
     * @var Browser
     */
    protected $browser;

    /**
     * Fixture Factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Enable Multiple wishlist in configuration
     *
     * @return void
     */
    public function __prepare()
    {
        $setupConfig = $this->objectManager->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'multiple_wishlist_default']
        );
        $setupConfig->run();
    }

    /**
     * Injection data
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CatalogProductView $catalogProductView
     * @param CustomerAccountLogout $customerAccountLogout
     * @param Browser $browser
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CatalogProductView $catalogProductView,
        CustomerAccountLogout $customerAccountLogout,
        Browser $browser,
        FixtureFactory $fixtureFactory
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->catalogProductView = $catalogProductView;
        $this->customerAccountLogout = $customerAccountLogout;
        $this->browser = $browser;
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Add Product to Multiple Wish list
     *
     * @param string $products
     * @param string $duplicate
     * @return array
     */
    public function test($products, $duplicate)
    {
        $this->markTestIncomplete('Bug: MAGETWO-27949');

        // Preconditions
        $multipleWishlist = $this->fixtureFactory->createByCode('multipleWishlist', ['dataSet' => 'wishlist_public']);
        $multipleWishlist->persist();
        $customer = $multipleWishlist->getDataFieldConfig('customer_id')['source']->getCustomer();

        list($fixture, $dataSet) = explode('::', $products);
        $product = $this->fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
        $product->persist();

        // Steps
        $this->cmsIndex->open();
        $this->cmsIndex->getLinksBlock()->openLink("Log In");
        $this->customerAccountLogin->getLoginBlock()->login($customer);
        $this->addToMultipleWishlist($product, $duplicate, $multipleWishlist);
        if ($duplicate == 'yes') {
            $this->addToMultipleWishlist($product, $duplicate, $multipleWishlist);
        }

        return [
            'product' => $product,
            'multipleWishlist' => $multipleWishlist,
            'customer' => $customer,
        ];
    }

    /**
     * Add product to multiple wishlist
     *
     * @param InjectableFixture $product
     * @param string $duplicate
     * @param MultipleWishlist $multipleWishlist
     * @return void
     */
    protected function addToMultipleWishlist($product, $duplicate, $multipleWishlist)
    {
        $this->browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $this->catalogProductView->getViewBlock()->fillOptions($product);
        if ($duplicate == 'yes') {
            $qty = $product->getCheckoutData()['options']['qty'] / 2;
            $this->catalogProductView->getViewBlock()->setQty($qty);
        }
        $this->catalogProductView->getMultipleWishlistViewBlock()->addToMultipleWishlist($multipleWishlist->getName());
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
