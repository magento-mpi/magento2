<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\TestCase;

use Magento\Wishlist\Test\TestCase\AddProductsToCartFromCustomerWishlistOnFrontendTest as AddProductsToCartFromWishlist;

/**
 * Test Creation for Adding Grouped product from Wishlist to Cart
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create customer and login to frontend
 * 2. Grouped product is created
 * 3. Add grouped product to customer's wishlist
 *
 * Steps:
 * 1. Navigate to My Account -> My Wishlist
 * 2. Fill qty and update wish list
 * 3. Click "Add to Cart"
 * 4. Perform asserts
 *
 * @group Wishlist_(CS)
 * @ZephyrId  MAGETWO-25268
 */
class AddGroupedProductToCartFromCustomerWishlistOnFrontendTest extends AddProductsToCartFromWishlist
{
    //
}
