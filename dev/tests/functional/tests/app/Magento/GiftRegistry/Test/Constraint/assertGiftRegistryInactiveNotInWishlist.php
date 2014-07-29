<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\GiftRegistry\Test\Fixture\GiftRegistry;
use Magento\Wishlist\Test\Page\WishlistIndex;

/**
 * Class AssertGiftRegistryInactiveNotInWishlist
 */
class AssertGiftRegistryInactiveNotInWishlist extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that product can not be added to active gift registry from Wishlist
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductSimple $product
     * @param GiftRegistry $giftRegistry
     * @param WishlistIndex $wishlistIndex
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogProductSimple $product,
        GiftRegistry $giftRegistry,
        WishlistIndex $wishlistIndex
    ) {
        $catalogProductView->init($product);
        $catalogProductView->open()->getViewBlock()->addToWishlist();
        \PHPUnit_Framework_Assert::assertFalse(
            $wishlistIndex->getWishlistBlock()->giftRegistryIsVisible($giftRegistry->getTitle()),
            'Product can be added to gift registry \'' . $giftRegistry->getTitle() . '\' from Wishlist.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product can not be added to active GiftRegistry from Wishlist.';
    }
}
