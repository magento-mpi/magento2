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
use Magento\Wishlist\Test\Page\SearchResult;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;

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
     * Assert that Wishlist can be or can't be find by another Customer (or guest) via "Wishlist Search"
     *
     * @param MultipleWishlist $multipleWishlist
     * @param CmsIndex $cmsIndex
     * @param CatalogCategory $category
     * @param CatalogCategoryView $catalogCategoryView
     * @param CustomerInjectable $customer
     * @param SearchResult $searchResult
     * @param CustomerAccountLogout $customerAccountLogout
     * @return void
     */
    public function processAssert(
        MultipleWishlist $multipleWishlist,
        CmsIndex $cmsIndex,
        CatalogCategory $category,
        CatalogCategoryView $catalogCategoryView,
        CustomerInjectable $customer,
        SearchResult $searchResult,
        CustomerAccountLogout $customerAccountLogout
    ) {
        $customerAccountLogout->open();
        $cmsIndex->open()->getTopmenu()->selectCategoryByName($category->getName());
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
    abstract protected function assert(SearchResult $searchResult, MultipleWishlist $multipleWishlist);
}
