<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\TestCase;

use Magento\Wishlist\Test\TestCase\MoveFromShoppingCardToWishlistTest;

/**
 * Test Creation for Move GiftCard Product from ShoppingCard to Wishlist
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
class MoveGiftCardProductFromShoppingCardToWishlistTest extends MoveFromShoppingCardToWishlistTest
{
    //
}
