<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\TestCase;

use Magento\Wishlist\Test\TestCase\ViewCustomerWishlistOnBackendTest;

/**
 * Test Creation for ViewGiftCardWishlistOnBackend
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create customer.
 * 2. Create gift card product from dataSet.
 * 3. Add gift card product to the customer's wish list (gift card product should be configured).
 *
 * Steps:
 * 1. Log in to backend.
 * 2. Go to Customers > All Customers.
 * 3. Search and open customer.
 * 4. Open wish list tab.
 * 5. Perform assertions.
 *
 * @group Wishlist_(CS)
 * @ZephyrId MAGETWO-29616
 */
class ViewGiftCardWishlistOnBackendTest extends ViewCustomerWishlistOnBackendTest
{
    //
}
