<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Wishlist\Test\Constraint;

use Mtf\Fixture\InjectableFixture;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Wishlist\Test\Page\WishlistIndex;

/**
 * Class AssertWishlistIsEmpty
 * Check that there are no Products in Wishlist
 */
class AssertWishlistIsEmpty extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Check that there are no Products in Wishlist
     *
     * @param InjectableFixture[] $products
     * @param CmsIndex $cmsIndex
     * @param WishlistIndex $wishlistIndex
     * @return void
     */
    public function processAssert(array $products, CmsIndex $cmsIndex, WishlistIndex $wishlistIndex)
    {
        $cmsIndex->getLinksBlock()->openLink("My Wish List");
        foreach ($products as $itemProduct) {
            \PHPUnit_Framework_Assert::assertFalse(
                $wishlistIndex->getItemsBlock()->getItemProduct($itemProduct)->isVisible(),
                '"' . $itemProduct->getName() . '" product is present in Wishlist.'
            );
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Wishlist is empty.';
    }
}
