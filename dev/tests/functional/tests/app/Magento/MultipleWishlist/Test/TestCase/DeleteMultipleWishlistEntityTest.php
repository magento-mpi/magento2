<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\MultipleWishlist\Test\TestCase;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;

/**
 * Test Creation for DeleteMultipleWishlistEntity
 *
 * Preconditions:
 * 1. Enable Multiple Wishlist functionality.
 * 2. Create Customer Account.
 *
 * Test Flow:
 * 1. Login to frontend as a Customer.
 * 2. Navigate to: My Account > My Wishlist.
 * 3. Create wishlist.
 * 4. Delete wishlist.
 * 5. Perform appropriate assertions.
 *
 * @group Multiple_Wishlists_(CS)
 * @ZephyrId MAGETWO-27253
 */
class DeleteMultipleWishlistEntityTest extends AbstractMultipleWishlistEntityTest
{
    /**
     * Delete Multiple Wishlist Entity
     *
     * @param MultipleWishlist $multipleWishlist
     * @param CustomerInjectable $customer
     * @param string $isCreateMultipleWishlist
     * @return void
     */
    public function test(MultipleWishlist $multipleWishlist, CustomerInjectable $customer, $isCreateMultipleWishlist)
    {
        // Steps
        if ($isCreateMultipleWishlist == 'No') {
            return;
        }
        $multipleWishlist = $this->createMultipleWishlist($multipleWishlist, $customer);
        $this->openWishlistPage($customer);
        $this->wishlistIndex->getManagementBlock()->selectedWishlistByName($multipleWishlist->getName());
        $this->wishlistIndex->getManagementBlock()->removeWishlist();
    }
}
