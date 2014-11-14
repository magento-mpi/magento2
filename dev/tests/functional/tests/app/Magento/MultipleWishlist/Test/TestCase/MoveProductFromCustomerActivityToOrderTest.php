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
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\Sales\Test\Page\Adminhtml\OrderCreateIndex;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;
use Magento\GroupedProduct\Test\Fixture\GroupedProductInjectable;

/**
 * Test Creation for MoveProductFromCustomerActivityToOrder
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
 * 1. Login to frontend as a Customer.
 * 2. Navigate to created product
 * 3. Select created wishlist and add product to it
 * 4. Go to Customers account on backend
 * 5. Choose your wishlist in dropdown
 * 6. Check "->" and click button Update Changes.
 * 7. Perform appropriate assertions.
 *
 * @group Multiple_Wishlists_(CS)
 * @ZephyrId MAGETWO-29530
 */
class MoveProductFromCustomerActivityToOrderTest extends Injectable
{
    /**
     * CustomerIndex page
     *
     * @var CustomerIndex
     */
    protected $customerIndex;

    /**
     * CustomerIndexEdit page
     *
     * @var CustomerIndexEdit
     */
    protected $customerIndexEdit;

    /**
     * OrderCreateIndex page
     *
     * @var OrderCreateIndex
     */
    protected $orderCreateIndex;

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
     * Injection data
     *
     * @param CustomerIndex $customerIndex
     * @param CustomerIndexEdit $customerIndexEdit
     * @param OrderCreateIndex $orderCreateIndex
     * @return void
     */
    public function __inject(
        CustomerIndex $customerIndex,
        CustomerIndexEdit $customerIndexEdit,
        OrderCreateIndex $orderCreateIndex
    ) {
        $this->customerIndex = $customerIndex;
        $this->customerIndexEdit = $customerIndexEdit;
        $this->orderCreateIndex = $orderCreateIndex;
    }

    /**
     * Move product from customer activity to order on backend
     *
     * @param MultipleWishlist $multipleWishlist
     * @param string $products
     * @param string $duplicate
     * @param string $qtyToMove
     * @return array
     */
    public function test(MultipleWishlist $multipleWishlist, $products, $duplicate, $qtyToMove)
    {
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

        $this->customerIndex->open();
        $this->customerIndex->getCustomerGridBlock()->searchAndOpen(['email' => $customer->getEmail()]);
        $this->customerIndexEdit->getPageActionsBlock()->createOrder();
        $this->orderCreateIndex->getMultipleWishlistBlock()->selectWishlist($multipleWishlist->getName());
        $wishlistItemsBlock = $this->orderCreateIndex->getMultipleWishlistBlock()->getWishlistItemsBlock();
        $wishlistItemsBlock->selectItemToAddToOrder($product, $qtyToMove);
        if (!$product instanceof GroupedProductInjectable) {
            $this->orderCreateIndex->getCustomerActivitiesBlock()->updateChanges();
        } else {
            $this->orderCreateIndex->getConfigureProductBlock()->clickOk();
        }

        return ['products' => [$product]];
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
