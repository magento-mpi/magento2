<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftCard\Test\TestCase;

use Magento\Wishlist\Test\TestCase\AddProductsToCartFromCustomerWishlistOnFrontendTest as AddProductsToCartFromWishlist;

/**
 * Test Creation for Adding Gift Card product from Wishlist to Cart
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create customer and login to frontend
 * 2. Gift card product is created
 * 3. Add gift card product to customer's wishlist
 *
 * Steps:
 * 1. Navigate to My Account -> My Wishlist
 * 2. Fill qty and update wish list
 * 3. Click "Add to Cart"
 * 4. Perform asserts
 *
 * @group Wishlist_(CS)
 * @ZephyrId MAGETWO-25268
 */
class AddGiftCardProductToCartFromCustomerWishlistOnFrontendTest extends AddProductsToCartFromWishlist
{
    //
}
