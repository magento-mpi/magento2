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
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\MultipleWishlist\Test\Page\CatalogCategoryView;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;

/**
 * Class AssertWishlistIsPublic
 * Assert that Wishlist can be find by another Customer (or guest) via "Wishlist Search".
 */
class AssertWishlistIsPublic extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'Medium';

    /**
     * Assert that Wishlist can be find by another Customer (or guest) via "Wishlist Search".
     *
     * @param MultipleWishlist $multipleWishlist
     * @param CmsIndex $cmsIndex
     * @param CatalogCategory $category
     * @param CatalogCategoryView $catalogCategoryView
     * @param CustomerInjectable $customer
     * @param SearchResult $searchResult
     * @return void
     */
    public function processAssert(
        MultipleWishlist $multipleWishlist,
        CmsIndex $cmsIndex,
        CatalogCategory $category,
        CatalogCategoryView $catalogCategoryView,
        CustomerInjectable $customer,
        SearchResult $searchResult
    ) {
        $this->logOut($cmsIndex);
        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategoryByName($category->getName());
        $catalogCategoryView->getWishlistSearchBlock()->searchByEmail($customer->getEmail());
        $this->assert($searchResult, $multipleWishlist);
    }

    /**
     * Assert wish list is public
     *
     * @param SearchResult $searchResult
     * @param MultipleWishlist $multipleWishlist
     * @return void
     */
    protected function assert(SearchResult $searchResult, MultipleWishlist $multipleWishlist)
    {
        \PHPUnit_Framework_Assert::assertTrue(
            $searchResult->getWishlistSearchResultBlock()->visibleInGrid($multipleWishlist->getName()),
            'Multiple wish list is not public.'
        );
    }

    /**
     * Log out customer
     *
     * @param CmsIndex $cmsIndex
     * @return void
     */
    protected function logOut(CmsIndex $cmsIndex)
    {
        $cmsIndex->open();
        if ($cmsIndex->getLinksBlock()->isLinkVisible('Log Out')) {
            $cmsIndex->getLinksBlock()->openLink('Log Out');
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Multiple wish list is public.';
    }
}
