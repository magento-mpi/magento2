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
        // Create magento configuration
        $config = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $config->switchData('enable_mysql_search');
        $config->persist();

        // Create customer
        $customer = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $customer->persist();

        /** @todo ACB add curl handlers for special price products */
        // Create simple product with special price
        $simpleProduct = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $simpleProduct->switchData('simple');
        $simpleProduct->persist();

        // Create configurable product with special price
        $configurableProduct = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $configurableProduct->switchData('configurable');
        $configurableProduct->persist();

        // Steps
        // Add simple & configurable products with special price to cart
        $this->addProductToCart($simpleProduct);
        $this->addProductToCart($configurableProduct);

        // Verifying
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
}
