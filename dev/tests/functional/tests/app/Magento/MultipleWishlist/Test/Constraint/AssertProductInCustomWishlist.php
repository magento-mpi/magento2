<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\GroupedProduct\Test\Fixture\GroupedProductInjectable;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;
use Magento\Wishlist\Test\Page\WishlistIndex;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\InjectableFixture;

/**
 * Class AssertProductInCustomWishlist
 * Assert that product is present in custom wishlist
 */
class AssertProductInCustomWishlist extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that product is present in custom wishlist
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountIndex $customerAccountIndex
     * @param MultipleWishlist $multipleWishlist
     * @param WishlistIndex $wishlistIndex
     * @param InjectableFixture $product
     * @param string $qtyToMove
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CustomerAccountIndex $customerAccountIndex,
        MultipleWishlist $multipleWishlist,
        WishlistIndex $wishlistIndex,
        InjectableFixture $product,
        $qtyToMove
    ) {
        $cmsIndex->getLinksBlock()->openLink('My Account');
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('My Wish List');
        $wishlistIndex->getManagementBlock()->selectedWishlistByName($multipleWishlist->getName());
        $formData = $wishlistIndex->getMultipleItemsBlock()->getItemProduct($product)->getData();
        $actualQuantity = $product instanceof GroupedProductInjectable ? '-' : $formData['qty'];

        \PHPUnit_Framework_Assert::assertEquals($qtyToMove, $actualQuantity);
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Product with correct quantity is present in custom wishlist';
    }
}
