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
    /**
     * Run Add GiftCardProduct To Wishlist test
     *
     * @param CustomerInjectable $customer
     * @param string $product
     * @return array
     */
    public function test(CustomerInjectable $customer, $product)
    {
        $this->markTestIncomplete('Bug: MAGETWO-27949');
        return parent::test($customer, $product);
    }
}
