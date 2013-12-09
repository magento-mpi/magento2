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

namespace Magento\Catalog\Test\TestCase\Product;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Catalog\Test\Fixture\Product;

/**
 * Apply minimum advertised price to simple product
 *
 * @package Magento\Catalog\Test\TestCase\Product
 */
class ApplyMAPTest extends Functional
{
    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Apply minimum advertised price to simple product
     *
     * @ZephyrId MAGETWO-12430
     */
    public function testApplyMAPToSimple()
    {
        //Preconditions
        $config = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $config->switchData('enable_map_config');
        $config->persist();
        //Data
        $simple = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $simple->switchData('simple_with_map');
        $simple->persist();
        //Flush cache
        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();
        $cachePage->getMessagesBlock()->assertSuccessMessage();
        //Verifying
        $this->verifyMapOnCategory($simple);
        $this->verifyMapOnProductView($simple);
        $this->verifyMapInShoppingCart($simple);
    }

    /**
     * Assert product MAP related data on category page
     *
     * @param Product $product
     */
    protected function verifyMapOnCategory($product)
    {
        //Pages
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        //Steps
        $frontendHomePage->open();
        $frontendHomePage->getTopmenu()->selectCategoryByName($product->getCategoryName());
        //Verification on category product list
        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertTrue($productListBlock->isProductVisible($product->getProductName()));
        $this->assertContains($product->getProductMapPrice(), $productListBlock->getOldPrice(),
            'MAP price is incorrect');
//        $productListBlock->openMapBlockOnCategoryPage();
//        $this->assertContains($product->getProductPrice(), $productListBlock->getActualPrice(),
//            'Actual price is incorrect');
    }

    /**
     * Assert product MAP related data on product view page
     *
     * @param $product
     */
    protected function verifyMapOnProductView($product)
    {
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productListBlock = $categoryPage->getListProductBlock();
        $productListBlock->openProductViewPage($product->getProductName());
        //Verification on product detail page
        $productViewBlock = $productPage->getViewBlock();
        $productViewBlock->openMapBlockOnProductPage();
        $this->assertEquals($product->getProductName(), $productViewBlock->getProductName());
        $this->assertContains($product->getProductMapPrice(), $productViewBlock->getOldPrice());
        $this->assertContains($product->getProductPrice(), $productViewBlock->getActualPrice());
    }

    /**
     * Assert product related data in Shopping Cart
     *
     * @param $product
     */
    protected function verifyMapInShoppingCart($product)
    {
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productViewBlock = $productPage->getViewBlock();
        $productViewBlock->addToCart($product);
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->getMessageBlock()->assertSuccessMessage();
        $checkoutCartPage->open();
        $unitPrice = $checkoutCartPage->getCartBlock()->getCartItemUnitPrice($product);
        $this->assertContains($product->getProductPrice(), $unitPrice, 'Incorrect unit price is displayed in Cart');
    }

    /**
     * Disable MAP on Config level
     */
    public static function tearDownAfterClass()
    {
        $config = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $config->switchData('disable_map_config');
        $config->persist();
    }
}
