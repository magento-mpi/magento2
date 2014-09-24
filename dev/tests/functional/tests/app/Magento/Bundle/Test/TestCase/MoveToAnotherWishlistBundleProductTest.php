<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\TestCase;

use Magento\MultipleWishlist\Test\TestCase\MoveToAnotherWishlistTest;

/**
 * Test Creation for MoveToAnotherWishlistBundleProduct
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Enable Multiple wishlist in config
 * 2. Register new customer
 * 3. Create one custom Wishlist
 * 4. Add bundle product with qty defined in dataset to default Wishlist
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
class MoveToAnotherWishlistBundleProductTest extends MoveToAnotherWishlistTest
{
    //
}
