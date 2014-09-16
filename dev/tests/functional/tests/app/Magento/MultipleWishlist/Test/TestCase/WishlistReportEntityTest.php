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
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use \Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;
use Magento\MultipleWishlist\Test\Page\MultipleWishlistIndex;
use Magento\MultipleWishlist\Test\Page\Adminhtml\CustomerWishlistReport;

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
     * @var MultipleWishlistIndex
     */
    protected $multipleWishlistIndex;

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
     * @param MultipleWishlistIndex $multipleWishlistIndex
     * @param CustomerAccountLogout $customerAccountLogout
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CatalogProductView $catalogProductView,
        CustomerAccountLogin $customerAccountLogin,
        MultipleWishlistIndex $multipleWishlistIndex,
        CustomerAccountLogout $customerAccountLogout
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->catalogProductView = $catalogProductView;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->multipleWishlistIndex = $multipleWishlistIndex;
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
        $customer = $multipleWishlist->getDataFieldConfig('customer_id')['source']->getCustomerId();

        // Steps
        $this->loginCustomer($customer);
        foreach ($products as $key => $product) {
            $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
            $this->catalogProductView->getMultipleWishlistViewBlock()->addToMultipleWishlist(
                $multipleWishlist->getName()
            );
            $description = $wishlist[$key]['description'];
            $this->multipleWishlistIndex->getManagementBlock()->fillDescription($product, $description);
            $this->multipleWishlistIndex->getManagementBlock()->updateWishlist();
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
}
