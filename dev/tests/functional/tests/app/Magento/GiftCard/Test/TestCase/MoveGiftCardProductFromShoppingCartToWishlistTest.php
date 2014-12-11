<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftCard\Test\TestCase;

use Magento\Wishlist\Test\TestCase\MoveProductFromShoppingCartToWishlistTest;

/**
 * Test Creation for Move GiftCard Product from ShoppingCart to Wishlist
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. GiftCard product is created.
 *
 * Steps:
 * 1. Add product to Shopping Cart.
 * 2. Call AssertAddProductToCartSuccessMessage.
 * 2. Click 'Move to Wishlist' button from Shopping Cart for added product.
 * 3. Perform asserts.
 *
 * @group Shopping_Cart_(CS)
 * @ZephyrId MAGETWO-29545
 */
class MoveGiftCardProductFromShoppingCartToWishlistTest extends MoveProductFromShoppingCartToWishlistTest
{
    //
}
