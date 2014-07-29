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
use Magento\GiftRegistry\Test\Page\GiftRegistryIndex;
use Magento\GiftRegistry\Test\Page\GiftRegistryItems;

/**
 * Class AssertGiftRegistryActiveInWishlist
 */
class AssertGiftRegistryActiveInWishlist extends AbstractConstraint
{
    /**
     * Success message after gift registry has been added
     */
    const SUCCESS_MESSAGE = 'The wish list item has been added to this gift registry.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that product can be added to active gift registry from Wishlist
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductSimple $product
     * @param GiftRegistry $giftRegistry
     * @param WishlistIndex $wishlistIndex
     * @param GiftRegistryIndex $giftRegistryIndex
     * @param GiftRegistryItems $giftRegistryItems
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogProductSimple $product,
        GiftRegistry $giftRegistry,
        WishlistIndex $wishlistIndex,
        GiftRegistryIndex $giftRegistryIndex,
        GiftRegistryItems $giftRegistryItems
    ) {
        $catalogProductView->init($product);
        $catalogProductView->open()->getViewBlock()->addToWishlist();
        $wishlistIndex->getWishlistBlock()->addToGiftRegistry($giftRegistry->getTitle());
        $actualMessage = $wishlistIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
        );
        $giftRegistryIndex->open()->getGiftRegistryGrid()->eventAction($giftRegistry->getTitle(), 'Manage Items');
        \PHPUnit_Framework_Assert::assertTrue(
            $giftRegistryItems->getGiftRegistryItemsBlock()->isProductInGrid($product->getName()),
            'Product can not be added to gift registry \'' . $giftRegistry->getTitle() . '\' from Wishlist.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product can be added to active GiftRegistry from Wishlist.';
    }
}
