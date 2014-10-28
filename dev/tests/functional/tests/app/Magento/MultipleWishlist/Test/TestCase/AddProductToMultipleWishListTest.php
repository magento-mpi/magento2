<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\TestCase;

use Mtf\ObjectManager;
use Mtf\TestCase\Injectable;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;

/**
 * Test Creation for AddProductToMultipleWishList
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create Product
 * 2. Enable Multiple Wishlist functionality
 * 3. Create Customer Account
 * 4. Create Wishlist
 *
 * Steps:
 * 1. Login to frontend as a customer
 * 2. Navigate to created product
 * 3. Select created wishlist and add product to it
 * 4. Perform appropriate assertions.
 *
 * @group Multiple_Wishlists_(CS)
 * @ZephyrId MAGETWO-29044
 */
class AddProductToMultipleWishListTest extends Injectable
{
    /**
     * Enable Multiple wishlist in configuration
     *
     * @return void
     */
    public function __prepare()
    {
        $setupConfig = $this->objectManager->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'multiple_wishlist_default']
        );
        $setupConfig->run();
    }

    /**
     * Add Product to Multiple Wish list
     *
     * @param MultipleWishlist $multipleWishlist
     * @param string $products
     * @param string $duplicate
     * @return array
     */
    public function test(MultipleWishlist $multipleWishlist, $products, $duplicate)
    {
        $this->markTestIncomplete('Bug: MAGETWO-27949');

        // Preconditions
        $multipleWishlist->persist();
        $customer = $multipleWishlist->getDataFieldConfig('customer_id')['source']->getCustomer();
        $createProductsStep = $this->objectManager->create(
            'Magento\Catalog\Test\TestStep\CreateProductsStep',
            ['products' => $products]
        );
        $product = $createProductsStep->run()['products'][0];

        // Steps
        $loginCustomer = $this->objectManager->create(
            'Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $customer]
        );
        $loginCustomer->run();
        $addProductToMultiplewishlist = $this->objectManager->create(
            'Magento\MultipleWishlist\Test\TestStep\AddProductToMultipleWishlistStep',
            ['product' => $product, 'duplicate' => $duplicate, 'multipleWishlist' => $multipleWishlist]
        );
        $addProductToMultiplewishlist->run();
        if ($duplicate == 'yes') {
            $addProductToMultiplewishlist = $this->objectManager->create(
                'Magento\MultipleWishlist\Test\TestStep\AddProductToMultipleWishlistStep',
                ['product' => $product, 'duplicate' => $duplicate, 'multipleWishlist' => $multipleWishlist]
            );
            $addProductToMultiplewishlist->run();
        }

        return [
            'product' => $product,
            'multipleWishlist' => $multipleWishlist,
            'customer' => $customer,
        ];
    }

    /**
     * Disable multiple wish list in config
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        $setupConfig = ObjectManager::getInstance()->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'multiple_wishlist_default', 'rollback' => true]
        );
        $setupConfig->run();
    }
}
