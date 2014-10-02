<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\TestCase;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Wishlist\Test\TestCase\AddProductToWishlistEntityTest;

/**
 * Test Creation for AddGiftCardProductToWishlistEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Customer is registered
 * 2. GiftCard Product is created
 *
 * Steps:
 * 1. Login as a customer
 * 2. Navigate to catalog page
 * 3. Add created product to Wishlist according to dataSet
 * 4. Perform all assertions
 *
 * @group Wishlist_(CS)
 * @ZephyrId MAGETWO-29045
 */
class AddGiftCardProductToWishlistEntityTest extends AddProductToWishlistEntityTest
{
    //
}
