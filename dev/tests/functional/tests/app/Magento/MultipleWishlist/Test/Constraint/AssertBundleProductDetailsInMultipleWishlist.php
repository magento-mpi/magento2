<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\MultipleWishlist\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;
use Magento\Wishlist\Test\Constraint\AssertBundleProductDetailsInWishlist;
use Magento\Wishlist\Test\Page\WishlistIndex;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\InjectableFixture;

/**
 * Class AssertBundleProductDetailsInMultipleWishlist
 * Assert that the correct option details are displayed on the "View Details" tool tip.
 */
class AssertBundleProductDetailsInMultipleWishlist extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that the correct option details are displayed on the "View Details" tool tip.
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountIndex $customerAccountIndex
     * @param WishlistIndex $wishlistIndex
     * @param InjectableFixture $product
     * @param FixtureFactory $fixtureFactory
     * @param MultipleWishlist $multipleWishlist
     * @param AssertBundleProductDetailsInWishlist $assertBundleProductDetailsInWishlist
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CustomerAccountIndex $customerAccountIndex,
        WishlistIndex $wishlistIndex,
        InjectableFixture $product,
        FixtureFactory $fixtureFactory,
        MultipleWishlist $multipleWishlist,
        AssertBundleProductDetailsInWishlist $assertBundleProductDetailsInWishlist
    ) {
        $cmsIndex->getLinksBlock()->openLink('My Account');
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('My Wish List');
        $wishlistIndex->getManagementBlock()->selectedWishlistByName($multipleWishlist->getName());
        $options = $assertBundleProductDetailsInWishlist->getOptions($product, $wishlistIndex, $fixtureFactory);

        \PHPUnit_Framework_Assert::assertEquals(
            $options['expectedOptions'],
            $options['actualOptions'],
            "Expected bundle options are not equals to actual."
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return "Expected bundle options are equal to actual.";
    }
}
