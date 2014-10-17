<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Wishlist\Test\TestCase;

use Magento\Checkout\Test\Constraint\AssertAddedProductToCartSuccessMessage;
use Magento\Checkout\Test\Page\CheckoutCart;
use Mtf\TestCase\Injectable;
use Mtf\Client\Driver\Selenium\Browser;
use Mtf\Fixture\InjectableFixture;
use Mtf\ObjectManager;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Test Creation for Move Product from ShoppingCart to Wishlist
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Test products are created.
 *
 * Steps:
 * 1. Add product to Shopping Cart.
 * 2. Call AssertAddProductToCartSuccessMessage.
 * 2. Click 'Move to Wishlist' button from Shopping Cart for added product.
 * 3. Perform asserts.
 *
 * @group Shopping_Cart_(CS)
 * @ZephyrId MAGETWO-29545
 */
class MoveProductFromShoppingCartToWishlistTest extends Injectable
{
    /**
     * Catalog product view page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Checkout Cart page
     *
     * @var CheckoutCart
     */
    protected $checkoutCart;

    /**
     * Browser object
     *
     * @var Browser
     */
    protected $browser;

    /**
     * ObjectManager object
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Prepare data for test
     *
     * @param CustomerInjectable $customer
     * @param Browser $browser
     * @param ObjectManager $objectManager
     * @return array
     */
    public function __prepare(CustomerInjectable $customer, Browser $browser, ObjectManager $objectManager)
    {
        $this->browser = $browser;
        $this->objectManager = $objectManager;
        $customer->persist();

        return ['customer' => $customer];
    }

    /**
     * Injection data
     *
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    public function __inject(
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart
    ) {
        $this->catalogProductView = $catalogProductView;
        $this->checkoutCart = $checkoutCart;
    }

    /**
     * Run Move from ShoppingCard to Wishlist test
     *
     * @param CustomerInjectable $customer
     * @param string $product
     * @param AssertAddedProductToCartSuccessMessage $assertAddedProductToCartSuccessMessage
     * @return array
     */
    public function test(
        CustomerInjectable $customer,
        $product,
        AssertAddedProductToCartSuccessMessage $assertAddedProductToCartSuccessMessage
    ) {
        // Preconditions:
        $product = $this->createProduct($product);
        $this->loginCustomer($customer);

        // Steps:
        $this->browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $this->catalogProductView->getViewBlock()->addToCart($product);
        $assertAddedProductToCartSuccessMessage->processAssert($this->checkoutCart, $product);
        $this->checkoutCart->getCartBlock()->getCartItem($product)->moveToWishlist();

        return ['product' => $product];
    }

    /**
     * Create product
     *
     * @param string $product
     * @return InjectableFixture
     */
    protected function createProduct($product)
    {
        $createProducts = $this->objectManager->create(
            'Magento\Catalog\Test\TestStep\CreateProductsStep',
            ['products' => $product]
        );
        return $createProducts->run()['products'][0];
    }

    /**
     * Login customer
     *
     * @param CustomerInjectable $customer
     * @return void
     */
    protected function loginCustomer(CustomerInjectable $customer)
    {
        $customerLogin = $this->objectManager->create(
            'Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $customer]
        );
        $customerLogin->run();
    }
}
