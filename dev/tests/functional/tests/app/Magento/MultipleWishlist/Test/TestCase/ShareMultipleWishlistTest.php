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
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Wishlist\Test\Page\WishlistIndex;
use Magento\Wishlist\Test\Page\WishlistShare;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;

/**
 * Test Creation for ShareMultipleWishlist
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create Customer Account
 * 2. Create custom wishlist
 * 3. Create product
 *
 * Steps:
 * 1. Log in to Frontend as a Customer
 * 2. Add product to custom Wish List
 * 3. Click "Share Wish List" button
 * 4. Fill in all data according to data set
 * 5. Click "Share Wishlist" button
 * 6. Perform all assertions
 *
 * @group Multiple_Wishlists_(CS)
 * @ZephyrId MAGETWO-28982
 */
class ShareMultipleWishlistTest extends Injectable
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
     * Catalog Product View Page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Wishlist index Page
     *
     * @var WishlistIndex
     */
    protected $wishlistIndex;

    /**
     * Wishlist Share Page
     *
     * @var WishlistShare
     */
    protected $wishlistShare;

    /**
     * Browser object
     *
     * @var Browser
     */
    protected $browser;

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
     * @param WishlistIndex $wishlistIndex
     * @param WishlistShare $wishlistShare
     * @param Browser $browser
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CatalogProductView $catalogProductView,
        WishlistIndex $wishlistIndex,
        WishlistShare $wishlistShare,
        Browser $browser
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->catalogProductView = $catalogProductView;
        $this->wishlistIndex = $wishlistIndex;
        $this->wishlistShare = $wishlistShare;
        $this->browser = $browser;
    }

    /**
     * Share Multiple Wish list
     *
     * @param CatalogProductSimple $product
     * @param MultipleWishlist $multipleWishlist
     * @param array $sharingInfo
     * @return void
     */
    public function test(
        CatalogProductSimple $product,
        MultipleWishlist $multipleWishlist,
        array $sharingInfo
    ) {
        // Preconditions
        $multipleWishlist->persist();
        $product->persist();
        $customer = $multipleWishlist->getDataFieldConfig('customer_id')['source']->getCustomer();

        // Steps
        $this->cmsIndex->open();
        $this->cmsIndex->getLinksBlock()->openLink("Log In");
        $this->customerAccountLogin->getLoginBlock()->login($customer);
        $this->browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $this->catalogProductView->getMultipleWishlistViewBlock()->addToMultipleWishlist($multipleWishlist->getName());
        $this->wishlistIndex->getWishlistBlock()->clickShareWishList();
        $this->wishlistShare->getSharingInfoForm()->fillForm($sharingInfo);
        $this->wishlistShare->getSharingInfoForm()->shareWishlist();
    }

    /**
     * Clear data after test
     *
     * @return void
     */
    public function tearDown()
    {
        $setupConfig = $this->objectManager->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'multiple_wishlist_default', 'rollback' => true]
        );
        $setupConfig->run();
    }
}
