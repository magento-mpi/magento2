<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Checkout\Test\Fixture\Cart;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Checkout\Test\Page\CheckoutCart;

/**
 * Test Creation for Update ShoppingCart
 *
 * Test Flow:
 * Precondition:
 * 1. Simple product is created
 * 2. Clear shopping cart
 *
 * Steps:
 * 1. Go to frontend
 * 2. Add product with qty = 1 to shopping cart
 * 3. Fill in all data according to data set
 * 4. Click "Update Shopping Cart" button
 * 5. Perform all assertion from dataset
 *
 * @group Shopping Cart (CS)
 * @ZephyrId MAGETWO-25081
 */
class UpdateShoppingCartTest extends Injectable
{
    /**
     * Page CatalogProductView
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Page CheckoutCart
     *
     * @var CheckoutCart
     */
    protected $checkoutCart;

    /**
     * Inject data
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
     * Create simple product with price "100"
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $product = $fixtureFactory->createByCode('catalogProductSimple', ['dataSet' => '100_dollar_product']);
        $product->persist();

        return [
            'product' => $product
        ];
    }

    /**
     * Update Shopping Cart
     *
     * @param Cart $cart
     * @param CatalogProductSimple $product
     * @return void
     */
    public function testUpdateShoppingCart(
        Cart $cart,
        CatalogProductSimple $product
    ) {
        // Preconditions
        $this->checkoutCart->open()->getCartBlock()->clearShoppingCart();

        // Steps
        $this->catalogProductView->init($product);
        $this->catalogProductView->open()->getViewBlock()->clickAddToCart();
        $this->checkoutCart->getCartBlock()->getCartItem($product)->setQty($cart->getQty());
        $this->checkoutCart->getCartBlock()->updateShoppingCart();
    }
}
