<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Class ProductSpecialPriceTest
 * Test checking out with a product that has special prices
 *
 * @package Magento\Test\TestCase
 */
class ProductSpecialPriceTest extends Functional
{
    /**
     * Place order on frontend that contains a product with special prices.
     *
     * @ZephyrId MAGETWO-12429
     */
    public function testProductSpecialPriceCheckout()
    {
        //Ensure shopping cart is empty
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();

        // Preconditions
        // Create customer
        $customer = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $customer->persist();

        // Create simple product with special price
        $simpleProduct = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $simpleProduct->switchData('simple_advanced_pricing');
        $simpleProduct->persist();

        // Create configurable product with special price
        $configurableProduct = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $configurableProduct->switchData('configurable_advanced_pricing');
        $configurableProduct->persist();

        // Steps
        // Add simple & configurable products with special price to cart
        $this->addProductToCart($simpleProduct);
        $this->addProductToCart($configurableProduct);

        // Verify cart contents
        $this->verifyCartItem($simpleProduct);
        //$this->verifyCartItem($configurableProduct);

        /** @todo ACB - step 7 - submit order via one page checkout */
        /** @todo ACB - step 8 & 9 - verify order in admin */
    }

    /**
     * Add a product to the cart.
     *
     * @param \Magento\Catalog\Test\Fixture\Product $product
     */
    private function addProductToCart(\Magento\Catalog\Test\Fixture\Product $product)
    {
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->init($product);
        $productPage->open();
        $productPage->getViewBlock()->addToCart($product);
    }

    /**
     * Verifies the unit price and subtotal for cart item.
     *
     * @param \Magento\Catalog\Test\Fixture\Product $product
     */
    private function verifyCartItem(\Magento\Catalog\Test\Fixture\Product $product)
    {
        $productName = $product->getProductName();
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();

        $specialPrice = $product->getProductSpecialPrice();
        $unitPrice = $checkoutCartPage->getCartBlock()->getCartItemUnitPrice($productName);
        $subTotal = $checkoutCartPage->getCartBlock()->getCartItemSubTotal($productName);
        $this->assertContains(
            $specialPrice,
            $unitPrice,
            'Incorrect unit price for ' . $productName
        );
        $this->assertContains(
            $specialPrice,
            $subTotal,
            'Incorrect sub-total for ' . $productName
        );
    }
}
