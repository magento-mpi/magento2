<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\MultipleWishlist\Test\Page\SearchResult;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;
use Magento\Wishlist\Test\Page\WishlistIndex;

/**
 * Abstract Class AbstractAssertMultipleWishlistState
 * Assert that Wish list can be or can't be find by another Customer (or guest) via "Wishlist Search"
 */
abstract class AbstractAssertMultipleWishlistState extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'Medium';

    /**
     * Notice type
     *
     * @var string
     */
    protected $noticeType;

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
     * Assert that Wishlist can be or can't be find by another Customer (or guest) via "Wishlist Search"
     *
     * @param MultipleWishlist $multipleWishlist
     * @param CmsIndex $cmsIndex
     * @param CatalogCategory $category
     * @param CatalogCategoryView $catalogCategoryView
     * @param CustomerInjectable $customer
     * @param SearchResult $searchResult
     * @param CustomerAccountLogout $customerAccountLogout
     * @param CustomerAccountIndex $customerAccountIndex
     * @param WishlistIndex $wishlistIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @return void
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function processAssert(
        MultipleWishlist $multipleWishlist,
        CmsIndex $cmsIndex,
        CatalogCategory $category,
        CatalogCategoryView $catalogCategoryView,
        CustomerInjectable $customer,
        SearchResult $searchResult,
        CustomerAccountLogout $customerAccountLogout,
        CustomerAccountIndex $customerAccountIndex,
        WishlistIndex $wishlistIndex,
        CustomerAccountLogin $customerAccountLogin
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->loginCustomer($customer);
        $cmsIndex->open()->getLinksBlock()->openLink('My Account');
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('My Wish List');
        $wishlistIndex->getManagementBlock()->selectedWishlistByName($multipleWishlist->getName());
        \PHPUnit_Framework_Assert::assertTrue(
            $wishlistIndex->getManagementBlock()->isNoticeTypeVisible($this->noticeType),
            'Notice type is not correct.'
        );

        $customerAccountLogout->open();
        $cmsIndex->open()->getTopmenu()->selectCategoryByName($category->getName());
        $catalogCategoryView->getWishlistSearchBlock()->searchByEmail($customer->getEmail());
        $this->assert($searchResult, $multipleWishlist);
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
        $linksBlock = $this->cmsIndex->getLinksBlock();
        if (!$linksBlock->isLinkVisible('Log Out')) {
            $linksBlock->openLink("Log In");
            $this->customerAccountLogin->getLoginBlock()->login($customer);
        }
    }

    /**
     * Assert wish list is public
     *
     * @param SearchResult $searchResult
     * @param MultipleWishlist $multipleWishlist
     * @return void
     */
    abstract protected function assert(SearchResult $searchResult, MultipleWishlist $multipleWishlist);
}
