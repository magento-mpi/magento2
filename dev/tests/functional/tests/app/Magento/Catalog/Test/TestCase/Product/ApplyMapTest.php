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
use Magento\Catalog\Test\Fixture\SimpleProduct;

/**
 * Apply minimum advertised price to simple product
 *
 * @package Magento\Catalog\Test\TestCase\Product
 */
class ApplyMapTest extends Functional
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
    public function testApplyMapToSimple()
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
     * @param \Magento\Catalog\Test\Fixture\SimpleProduct $product
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
        $mapBlock = $categoryPage->getMapBlock();
        $this->assertTrue($productListBlock->isProductVisible($product->getProductName()),
            'Product is invisible on Category page');
        $this->assertContains($product->getProductMapPrice(), $productListBlock->getOldPriceCategoryPage(),
            'Displayed on Category page MAP is incorrect');
        $productListBlock->openMapBlockOnCategoryPage($product->getProductName());
        $this->assertContains($product->getProductMapPrice(), $mapBlock->getOldPrice(),
        'Displayed on Category page MAP is incorrect');
        $this->assertEquals($product->getProductPrice(), $mapBlock->getActualPrice(),
            'Displayed on Category page price is incorrect');
    }

    /**
     * Assert product MAP related data on product view page
     *
     * @param SimpleProduct $product
     */
    protected function verifyMapOnProductView($product)
    {
        //Pages
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        //Steps
        $productListBlock = $categoryPage->getListProductBlock();
        $productListBlock->openProductViewPage($product->getProductName());
        $productViewBlock = $productPage->getViewBlock();
        $productViewBlock->openMapBlockOnProductPage();
        $mapBlock = $productPage->getMapBlock();
        //Verification on Product View page
        $this->assertContains($product->getProductMapPrice(), $mapBlock->getOldPrice(),
            'Displayed on Product page MAP is incorrect');
        $this->assertEquals($product->getProductPrice(), $mapBlock->getActualPrice(),
            'Displayed on Product page price is incorrect');
    }

    /**
     * Assert product related data in Shopping Cart
     *
     * @param SimpleProduct $product
     */
    protected function verifyMapInShoppingCart($product)
    {
        //Pages
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        //Steps
        $mapBlock = $productPage->getMapBlock();
        $mapBlock->addToCartFromMap();
        $checkoutCartPage->getMessageBlock()->assertSuccessMessage();
        //Verification in Shopping Cart
        $unitPrice = $checkoutCartPage->getCartBlock()->getCartItemUnitPrice($product);
        $this->assertEquals($product->getProductPrice(), $unitPrice, 'Incorrect unit price is displayed in Cart');
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
