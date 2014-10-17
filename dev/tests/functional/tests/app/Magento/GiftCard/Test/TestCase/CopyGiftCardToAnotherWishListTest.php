<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\TestCase;

use Magento\MultipleWishlist\Test\TestCase\CopyProductToAnotherWishListTest;

/**
 * Test Creation for CopyProductToAnotherWishList
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Enable Multiple wishlist in config.
 * 2. Create customer.
 * 3. Create one multiple wish list.
 * 4. Add gift card product with qty defined in dataSet to default wish list.
 *
 * Steps:
 * 1. Log in on frontend.
 * 2. Open default wish list.
 * 3. Check gift card product.
 * 4. Set qtyToCopy and copy it to another wish list.
 * 5. Perform assertions.
 *
 * @group Multiple_Wishlists_(CS)
 * @ZephyrId MAGETWO-29640
 */
class CopyGiftCardToAnotherWishListTest extends CopyProductToAnotherWishListTest
{
    //
}
