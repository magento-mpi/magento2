<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\TestCase;

use Mtf\Client\Browser;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
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
     * Fixture Factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

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
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __inject(
        CatalogProductView $catalogProductView,
        Browser $browser,
        FixtureFactory $fixtureFactory
    ) {
        $this->catalogProductView = $catalogProductView;
        $this->browser = $browser;
        $this->fixtureFactory = $fixtureFactory;
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
        $products = $createProductsStep->run()['products'];

        // Steps
        $loginCustomer = $this->objectManager->create(
            'Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $customer]
        );
        $loginCustomer->run();
        $this->addToMultipleWishlist($products, $duplicate, $multipleWishlist);
        if ($duplicate == 'yes') {
            $this->addToMultipleWishlist($products, $duplicate, $multipleWishlist);
        }

        return [
            'product' => $products[0],
            'multipleWishlist' => $multipleWishlist,
            'customer' => $customer,
        ];
    }

    /**
     * Add product to multiple wishlist
     *
     * @param array $products
     * @param string $duplicate
     * @param MultipleWishlist $multipleWishlist
     * @return void
     */
    protected function addToMultipleWishlist(array $products, $duplicate, MultipleWishlist $multipleWishlist)
    {
        foreach ($products as $product) {
            $this->browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
            $this->catalogProductView->getViewBlock()->fillOptions($product);

            $checkoutData = $product->getCheckoutData();
            if (isset($checkoutData['options']['qty'])) {
                $qty = $duplicate === 'yes'
                    ? $checkoutData['options']['qty'] / 2
                    : $checkoutData['options']['qty'] ;
                $this->catalogProductView->getViewBlock()->setQty($qty);
            }
            $this->catalogProductView->getMultipleWishlistViewBlock()->addToMultipleWishlist($multipleWishlist);
        }
    }
}
