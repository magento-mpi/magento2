<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\TestCase;

use Mtf\ObjectManager;
use Mtf\Client\Browser;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\InjectableFixture;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
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
     * Catalog Product View Page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Browser object
     *
     * @var Browser
     */
    protected $browser;

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
     * @param CatalogProductView $catalogProductView
     * @param Browser $browser
     * @return void
     */
    public function __inject(
        CatalogProductView $catalogProductView,
        Browser $browser
    ) {
        $this->catalogProductView = $catalogProductView;
        $this->browser = $browser;
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
        $this->addToMultipleWishlist($product, $duplicate, $multipleWishlist);
        if ($duplicate == 'yes') {
            $this->addToMultipleWishlist($product, $duplicate, $multipleWishlist);
        }

        return [
            'product' => $product,
            'multipleWishlist' => $multipleWishlist,
            'customer' => $customer,
        ];
    }

    /**
     * Add product to multiple wishlist
     *
     * @param InjectableFixture $product
     * @param string $duplicate
     * @param MultipleWishlist $multipleWishlist
     * @return void
     */
    protected function addToMultipleWishlist(InjectableFixture $product, $duplicate, MultipleWishlist $multipleWishlist)
    {
        $this->browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $this->catalogProductView->getViewBlock()->fillOptions($product);
        $checkoutData = $product->getCheckoutData();
        if (isset($checkoutData['qty'])) {
            $qty = $duplicate === 'yes'
                ? $checkoutData['qty'] / 2
                : $checkoutData['qty'];
            $this->catalogProductView->getViewBlock()->setQty($qty);
        }
        $this->catalogProductView->getMultipleWishlistViewBlock()->addToMultipleWishlist($multipleWishlist);
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
            ['configData' => 'disabled_multiple_wishlist_default']
        );
        $setupConfig->run();
    }
}
