<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\MultipleWishlist\Test\TestCase;

use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;
use Magento\MultipleWishlist\Test\Page\Adminhtml\CustomerWishlistReport;
use Magento\Wishlist\Test\Page\WishlistIndex;
use Mtf\Client\Browser;
use Mtf\Fixture\FixtureFactory;
use Mtf\ObjectManager;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for WishlistReportEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Simple products are created.
 * 3. Enable Multiple wishlist in configuration.
 * 4. Create Multiple wishlist.
 *
 * Steps:
 * 1. Login to backend as admin.
 * 2. Several created products are added to private Wish List.
 * 3. Several created products are added to public Wish List.
 * 4. Use the main menu "REPORTS" -> "Customers" -> "Wish Lists".
 * 5. Perform assertions.
 *
 * @group Reports_(MX)
 * @ZephyrId MAGETWO-27346
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class WishlistReportEntityTest extends Injectable
{
    /**
     * Fixture factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Customer Wish List Report Page
     *
     * @var CustomerWishlistReport
     */
    protected $customerWishlistReport;

    /**
     * CmsIndex Page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * CatalogProductView Page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Customer login page
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * My Wish Lists page
     *
     * @var WishlistIndex
     */
    protected $wishlistIndex;

    /**
     * CustomerAccountLogout Page
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Enable Multiple wishlist in configuration and create simple products
     *
     * @param CatalogProductSimple $productSimple1
     * @param CatalogProductSimple $productSimple2
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(
        CatalogProductSimple $productSimple1,
        CatalogProductSimple $productSimple2,
        FixtureFactory $fixtureFactory
    ) {
        $config = $fixtureFactory->createByCode('configData', ['dataSet' => 'multiple_wishlist_default']);
        $config->persist();

        $productSimple1->persist();
        $productSimple2->persist();

        return ['products' => [$productSimple1, $productSimple2]];
    }

    /**
     * Injection data
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogProductView $catalogProductView
     * @param CustomerAccountLogin $customerAccountLogin
     * @param WishlistIndex $wishlistIndex
     * @param CustomerAccountLogout $customerAccountLogout
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CatalogProductView $catalogProductView,
        CustomerAccountLogin $customerAccountLogin,
        WishlistIndex $wishlistIndex,
        CustomerAccountLogout $customerAccountLogout
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->catalogProductView = $catalogProductView;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->wishlistIndex = $wishlistIndex;
        $this->customerAccountLogout = $customerAccountLogout;
    }

    /**
     * Add products to multiple wishlist
     *
     * @param MultipleWishlist $multipleWishlist
     * @param Browser $browser
     * @param array $products
     * @param array $wishlist
     * @return array
     */
    public function test(MultipleWishlist $multipleWishlist, Browser $browser, array $products, array $wishlist)
    {
        // Precondition
        $multipleWishlist->persist();
        $customer = $multipleWishlist->getDataFieldConfig('customer_id')['source']->getCustomer();

        // Steps
        $this->loginCustomer($customer);
        foreach ($products as $key => $product) {
            $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
            $this->catalogProductView->getMultipleWishlistViewBlock()->addToMultipleWishlist($multipleWishlist);
            $this->wishlistIndex->getMultipleItemsBlock()->getItemProduct($product)
                ->fillProduct($wishlist[$key]);
            $this->wishlistIndex->getWishlistBlock()->clickUpdateWishlist();
        }

        return ['customer' => $customer];
    }

    /**
     * Login/Logout customer
     *
     * @param CustomerInjectable $customer
     * @return void
     */
    protected function loginCustomer(CustomerInjectable $customer)
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getLinksBlock()->openLink("Log In");
        $this->customerAccountLogin->getLoginBlock()->login($customer);
    }

    /**
     * Clear data after test
     *
     * @return void
     */
    public function tearDown()
    {
        $this->customerAccountLogout->open();
    }

    /**
     * Disable multiple wish list in config
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        ObjectManager::getInstance()->create(
            '\Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'multiple_wishlist_default', 'rollback' => true]
        )->run();
    }
}
