<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\TestCase;

use Magento\Wishlist\Test\TestCase\ConfigureCustomerWishlistOnBackendTest as ConfigureWishlistOnBackendTest;

/**
 * Test Creation for ConfigureCustomerWishlistOnBackend
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create customer
 * 2. Create gift card product
 * 3. Add gift card product to the customer's wishlist (unconfigured)
 *
 * Steps;
 * 1. Go to Backend
 * 2. Go to Customers > All Customers
 * 3. Open the customer
 * 4. Open wishlist tab
 * 5. Click 'Configure' for the gift card product
 * 6. Fill in data
 * 7. Click Ok
 * 8. Perform assertions
 *
 * @group Wishlist_(CS)
 * @ZephyrId MAGETWO-29257
 */
class ConfigureCustomerWishlistOnBackendTest extends ConfigureWishlistOnBackendTest
{
    //
}
