<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftCard\Test\TestCase;

use Magento\Checkout\Test\TestCase\DeleteProductsFromShoppingCartTest;

/**
 * Class DeleteGiftCardFromShoppingCartTest
 * Test delete GiftCard from shopping cart
 *
 * Preconditions
 * 1. Test GiftCard product is created
 *
 * Steps:
 * 1. Add product to Shopping Cart
 * 2. Click 'Remove item' button from Shopping Cart for product
 * 3. Perform all asserts
 *
 * @group Shopping_Cart_(CS)
 * @ZephyrId MAGETWO-25218
 */
class DeleteGiftCardFromShoppingCartTest extends DeleteProductsFromShoppingCartTest
{
    //
}
