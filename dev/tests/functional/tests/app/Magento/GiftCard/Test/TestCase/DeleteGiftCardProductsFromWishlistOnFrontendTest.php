<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\TestCase;

use Magento\Wishlist\Test\TestCase\DeleteProductsFromWishlistOnFrontendTest;

/**
 * Test Creation for DeleteProductsFromWishlist
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Customer registered
 * 2. Create GiftCard product
 *
 * Steps:
 * 1. Login as customer
 * 2. Add GiftCard product to Wishlist
 * 3. Navigate to My Account -> My Wishlist
 * 4. Click "Remove item"
 * 5. Perform all assertions
 *
 * @group Wishlist_(CS)
 * @ZephyrId MAGETWO-28874
 */
class DeleteGiftCardProductsFromWishlistOnFrontendTest extends DeleteProductsFromWishlistOnFrontendTest
{
    //
}
