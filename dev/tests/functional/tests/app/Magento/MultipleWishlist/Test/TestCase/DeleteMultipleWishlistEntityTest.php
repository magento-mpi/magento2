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
 * 1. Enable Multiple Wishlist functionality(see attachment "MultipleWishListSystemConfig.php").
 * 2. Create Customer Account.
 * 3. Preset for creation MultipleWishlist
 *
 * Test Flow:
 * 1. Login to frontend as a Customer.
 * 2. Navigate to: My Account > My Wishlist.
 * 3. Start creating Wishlist.
 * 4. Fill in data according to attached data set.
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
     * @param $isCreate
     * return void
     */
    public function test(MultipleWishlist $multipleWishlist, CustomerInjectable $customer, $isCreate)
    {
        // Steps
        $this->openWishlistPage($customer);
        if ($isCreate == 'No') {
            return;
        }
        $multipleWishlist = $this->createMultipleWishlist($multipleWishlist, $customer);
        $this->openWishlistPage();
        $this->multipleWishlistIndex->getManagementBlock()->selectedWishlistByName($multipleWishlist->getName());
        $this->multipleWishlistIndex->getManagementBlock()->removeWishlist();
    }

    /**
     * Inactive multiple wish list in config and delete wish list search widget
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
