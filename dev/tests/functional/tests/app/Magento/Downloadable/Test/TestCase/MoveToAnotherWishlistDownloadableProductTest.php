<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\TestCase;

use Magento\Downloadable\Test\Fixture\DownloadableProductInjectable;
use Magento\MultipleWishlist\Test\TestCase\AbstractMoveToAnotherWishlist;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;

/**
 * Test Creation for MoveToAnotherWishlistDownloadableProduct
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Enable Multiple wishlist in config
 * 2. Register new customer
 * 3. Create one custom Wishlist
 * 4. Add downloadable product with qty defined in dataset to default Wishlist
 *
 * Steps:
 * 1. Login to the Frontend as a customer
 * 2. Open default wishlist
 * 3. Set qtyToMove and Move it to custom wishlist
 * 4. Perform assertions
 *
 * @group Multiple_Wishlists_(CS)
 * @ZephyrId MAGETWO-28820
 */
class MoveToAnotherWishlistDownloadableProductTest extends AbstractMoveToAnotherWishlist
{
    /**
     * Run Move To Another Wishlist test
     *
     * @param MultipleWishlist $multipleWishlist
     * @param DownloadableProductInjectable $product
     * @param string $qtyToMove
     * @return array
     */
    public function test(MultipleWishlist $multipleWishlist, DownloadableProductInjectable $product, $qtyToMove)
    {
        return parent::moveToCustomWishlist($multipleWishlist, $product, $qtyToMove);
    }
}
