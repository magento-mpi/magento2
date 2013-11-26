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

namespace Magento\CatalogRule\Test\TestCase\CatalogPriceRule;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Class ApplyCatalogPriceRule
 *
 * @package Magento\CatalogRule\Test\TestCase\CatalogPriceRule
 */
class ApplyCatalogPriceRule extends Functional
{
    /**
     * Apply Catalog Price Rule to Products
     *
     * @ZephyrId MAGETWO-12389
     */
    public function testApplyCatalogPriceRule()
    {
        // Create Simple Product
        $simple = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $simple->switchData('simple');
        $simple->persist();

        // Create Configurable Product
        $configurable = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $configurable->switchData('configurable');
        $configurable->persist();

        // Create Customer
        $customer = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $customer->switchData('customer_US_1');
        $customer->persist();

        // Create Banner
        $banner = Factory::getFixtureFactory()->getMagentoBannerBanner();
        $banner->persist();

        // Create Frontend App
        $frontendApp = Factory::getFixtureFactory()->getMagentoWidgetInstance();
        $frontendApp->persist();

        // Create and Apply new Catalog Price

        // Update Banner with related Promotion

        // Verify applied catalog price rules
        //$this->assertOnCategory($products);
    }

    /**
     * Assert price on category and product page
     *
     * @param Product $product
     */
    protected function assertOnCategory($product)
    {
        //Pages
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        //Steps
        $frontendHomePage->open();
        $frontendHomePage->getTopmenu()->selectCategoryByName($product->getCategoryName());

        //Verify rule applied to price on Category page
        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertTrue($productListBlock->isProductVisible($product->getProductName()),
            'Product ' . $product->getProductName() . ' missing on category page.');
        // TODO: Apply catalog price rule
        $priceAppliedRule = $product->getProductPrice();
        $this->assertContains($priceAppliedRule, $productListBlock->getProductPrice(), 'Invalid product price.');

        //Verify rule applied to price on Product page
        $productViewBlock = $productPage->getViewBlock();
        $productListBlock->openProductViewPage($product->getProductName());
        $this->assertContains($priceAppliedRule, $productViewBlock->getProductPrice(), 'Invalid product price.');

        // Add products to Cart
        // Verify rule applied to products on shopping cart
        // Go to One Page Checkout
        // Verify rule applied to products during checkout
    }
}
