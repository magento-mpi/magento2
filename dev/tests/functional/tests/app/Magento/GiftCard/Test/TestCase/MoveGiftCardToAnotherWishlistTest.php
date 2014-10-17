<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\TestCase;

use Magento\MultipleWishlist\Test\TestCase\MoveProductToAnotherWishlistTest;

/**
 * Test Creation for MoveGiftCardToAnotherWishlistTest
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Enable Multiple wish list in config.
 * 2. Register new customer.
 * 3. Create one custom wish list.
 * 4. Add gift card product with qty defined in dataSet to default wish list.
 *
 * Steps:
 * 1. Login to the Frontend as a customer.
 * 2. Open default wish list.
 * 3. Set qtyToMove and move gift card product to custom wish list.
 * 4. Perform assertions.
 *
 * @group Multiple_Wishlists_(CS)
 * @ZephyrId MAGETWO-28820
 */
class MoveGiftCardToAnotherWishlistTest extends MoveProductToAnotherWishlistTest
{
    //
}
