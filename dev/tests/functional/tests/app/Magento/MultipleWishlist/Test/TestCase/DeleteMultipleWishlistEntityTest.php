<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\TestCase;

use Mtf\ObjectManager;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;

/**
 * Test Creation for DeleteMultipleWishlistEntity
 *
 * Preconditions:
 * 1. Enable Multiple Wishlist functionality(see attachment file "MultipleWishListSystemConfig.php").
 * 2. Create Customer Account.
 *
 * Test Flow:
 * 1. Login to frontend as a Customer.
 * 2. Navigate to: My Account > My Wishlist.
 * 3. Creating Wishlist.
 * 5. Delete wishlist.
 * 6. Perform appropriate assertions.
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
        $this->openWishlistPage($customer);
        if ($isCreateMultipleWishlist == 'No') {
            return;
        }
        $multipleWishlist = $this->createMultipleWishlist($multipleWishlist, $customer);
        $this->openWishlistPage();
        $this->multipleWishlistIndex->getManagementBlock()->selectedWishlistByName($multipleWishlist->getName());
        $this->multipleWishlistIndex->getManagementBlock()->removeWishlist();
    }

    /**
     * Inactive multiple wish list in config
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        $config = ObjectManager::getInstance()->create(
            'Magento\Core\Test\Fixture\ConfigData',
            ['dataSet' => 'disabled_multiple_wishlist_default']
        );
        $config->persist();
    }
}
