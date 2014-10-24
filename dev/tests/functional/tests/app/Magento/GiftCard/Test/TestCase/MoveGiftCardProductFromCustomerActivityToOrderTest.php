<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\TestCase;

use Magento\MultipleWishlist\Test\TestCase\MoveProductFromCustomerActivityToOrderTest;

/**
 * Test Creation for MoveProductFromCustomerActivityToOrder
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create Gift Card Product
 * 2. Enable Multiple Wishlist functionality
 * 3. Create Customer Account
 * 4. Create Wishlist
 *
 * Steps:
 * 1. Login to frontend as a Customer.
 * 2. Navigate to created product
 * 3. Select created wishlist and add product to it
 * 4. Go to Customers account on backend
 * 5. Choose your wishlist in dropdown
 * 6. Check "->" and click button Update Changes.
 * 7. Perform appropriate assertions.
 *
 * @group Multiple_Wishlists_(CS)
 * @ZephyrId MAGETWO-29530
 */
class MoveGiftCardProductFromCustomerActivityToOrderTest extends MoveProductFromCustomerActivityToOrderTest
{
    //
}
