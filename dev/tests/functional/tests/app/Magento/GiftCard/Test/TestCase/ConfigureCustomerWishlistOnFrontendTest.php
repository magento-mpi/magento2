<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\TestCase;

use Magento\Wishlist\Test\TestCase\ConfigureCustomerWishlistOnFrontendTest as ConfigureWishlistOnFrontendTest;

/**
 * Test Creation for ConfigureCustomerWishlist on frontend
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create customer
 * 2. Create gift card product
 * 3. Log in to frontend
 * 4. Add gift card product to the customer's wishlist (unconfigured)
 *
 * Steps:
 * 1. Open Wishlist
 * 2. Click 'Configure' for the gift card product
 * 3. Fill data
 * 4. Click 'Ok'
 * 5. Perform assertions
 *
 * @group Wishlist_(CS)
 * @ZephyrId MAGETWO-29507
 */
class ConfigureCustomerWishlistOnFrontendTest extends ConfigureWishlistOnFrontendTest
{
    //
}
