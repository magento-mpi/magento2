<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Wishlist\Test\Page\WishlistIndex;
use Magento\Wishlist\Test\Page\WishlistShare;

/**
 * Test Creation for ShareWishlistEntity
 *
 * Preconditions:
 * 1. Enable Multiple Wishlist functionality
 * 2. Create Customer Account
 * 3. Create product
 *
 * Test Flow:
 * 1. Login to frontend as a Customer
 * 2. Add product to Wish List
 * 3. Click "Share Wish List" button
 * 4. Fill in all data according to data set
 * 5. Click "Share Wishlist" button
 * 6. Perform all assertions
 *
 * @group Wishlist_(CS)
 * @ZephyrId MAGETWO-23394
 */
class ShareWishlistEntityTest extends Injectable
{
    /**
     * Cms index page
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
     * Customer account index page
     *
     * @var CustomerAccountIndex
     */
    protected $customerAccountIndex;

    /**
     * Product view page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Page CustomerAccountLogout
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Wishlist index page
     *
     * @var WishlistIndex
     */
    protected $wishlistIndex;

    /**
     * Wishlist share page
     *
     * @var WishlistShare
     */
    protected $wishlistShare;

    /**
     * Prepare data
     *
     * @param FixtureFactory $fixtureFactory
     * @param CustomerInjectable $customer
     * @param CatalogProductSimple $product
     * @return array
     */
    public function __prepare(
        FixtureFactory $fixtureFactory,
        CustomerInjectable $customer,
        CatalogProductSimple $product
    ) {
        $fixtureFactory->createByCode('configData', ['dataSet' => 'multiple_wishlist_default'])->persist();
        $customer->persist();
        $product->persist();

        return [
            'customer' => $customer,
            'product' => $product
        ];
    }

    /**
     * Injection data
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountIndex $customerAccountIndex
     * @param CustomerAccountLogout $customerAccountLogout
     * @param CatalogProductView $catalogProductView
     * @param WishlistIndex $wishlistIndex
     * @param WishlistShare $wishlistShare
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountIndex $customerAccountIndex,
        CustomerAccountLogout $customerAccountLogout,
        CatalogProductView $catalogProductView,
        WishlistIndex $wishlistIndex,
        WishlistShare $wishlistShare
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->customerAccountIndex = $customerAccountIndex;
        $this->customerAccountLogout = $customerAccountLogout;
        $this->catalogProductView = $catalogProductView;
        $this->wishlistIndex = $wishlistIndex;
        $this->wishlistShare = $wishlistShare;
    }

    /**
     * Share wish list
     *
     * @param CustomerInjectable $customer
     * @param CatalogProductSimple $product
     * @param array $sharingInfo
     * @return void
     */
    public function test(CustomerInjectable $customer, CatalogProductSimple $product, $sharingInfo)
    {
        //Steps
        $this->loginCustomer($customer);
        $this->catalogProductView->init($product);
        $this->catalogProductView->open()->getViewBlock()->addToWishlist();
        $this->wishlistIndex->getWishlistBlock()->clickShareWishList();
        $this->wishlistShare->getSharingInfoForm()->fillForm($sharingInfo);
        $this->wishlistShare->getSharingInfoForm()->shareWishlist();
    }

    /**
     * Login customer
     *
     * @param CustomerInjectable $customer
     * @return void
     */
    protected function loginCustomer(CustomerInjectable $customer)
    {
        $this->cmsIndex->open();
        if (!$this->cmsIndex->getLinksBlock()->isLinkVisible('Log Out')) {
            $this->cmsIndex->getLinksBlock()->openLink("Log In");
            $this->customerAccountLogin->getLoginBlock()->login($customer);
        }
    }

    /**
     * Log out after test
     *
     * @return void
     */
    public function tearDown()
    {
        $this->customerAccountLogout->open();
    }
}
