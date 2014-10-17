<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\TestCase;

use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Test Creation for CopyProductToAnotherWishList
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Enable Multiple wish list in config.
 * 2. Create customer.
 * 3. Create one multiple wish list.
 * 4. Add product with qty defined in dataSet to default wish list.
 *
 * Steps:
 * 1. Log in on frontend.
 * 2. Open default wish list.
 * 3. Check product.
 * 4. Set qtyToCopy and copy it to another wish list.
 * 5. Perform assertions.
 *
 * @group Multiple_Wishlists_(CS)
 * @ZephyrId MAGETWO-29640
 */
class CopyProductToAnotherWishListTest extends AbstractActionProductToAnotherWishlistTest
{
    /**
     * Action for this test.
     *
     * @var string
     */
    protected $action = 'copy';

    /**
     * Run Move To Another Wishlist test.
     *
     * @param MultipleWishlist $multipleWishlist
     * @param CustomerInjectable $customer
     * @param string $product
     * @param int $qty
     * @param int $qtyToAction
     * @return array
     */
    public function test(MultipleWishlist $multipleWishlist, CustomerInjectable $customer, $product, $qty, $qtyToAction)
    {
        // Preconditions
        $this->createMultipleWishlist($multipleWishlist, $customer);
        $product = $this->createProduct($product, $qty);
        $this->loginCustomer($customer);
        $this->addProductToWishlist($product);

        // Steps
        $this->actionProductToAnotherWishlist($multipleWishlist, $product, $qtyToAction);

        return ['product' => $product, 'typeAction' => $this->action];
    }
}
