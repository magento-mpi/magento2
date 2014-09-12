<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Catalog\Test\Fixture\SimpleProduct;

/**
 * Apply minimum advertised price to simple product
 */
class ApplyMapTest extends Functional
{
    /**
     * Login into backend area before test
     *
     * @return void
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Apply minimum advertised price to simple product
     *
     * @ZephyrId MAGETWO-12430
     * @return void
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
     * @param SimpleProduct $product
     * @return void
     */
    protected function verifyMapOnCategory(SimpleProduct $product)
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
        $this->assertTrue(
            $productListBlock->isProductVisible($product->getName()),
            'Product is invisible on Category page'
        );

        $this->assertContains(
            $product->getProductMapPrice(),
            $productListBlock->getOldPriceCategoryPage(),
            'Displayed on Category page MAP is incorrect'
        );

        $productListBlock->openMapBlockOnCategoryPage($product->getName());
        $this->assertContains(
            $product->getProductMapPrice(),
            $mapBlock->getMapOldPrice(),
            'Displayed on Category page MAP is incorrect'
        );
        $this->assertEquals(
            $product->getProductPrice(),
            $mapBlock->getActualPrice(),
            'Displayed on Category page price is incorrect'
        );
    }

    /**
     * Assert product MAP related data on product view page
     *
     * @param SimpleProduct $product
     * @return void
     */
    protected function verifyMapOnProductView($product)
    {
        //Pages
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        //Steps
        $productListBlock = $categoryPage->getListProductBlock();
        $productListBlock->openProductViewPage($product->getName());
        $productViewBlock = $productPage->getViewBlock();
        $productViewBlock->openMapBlockOnProductPage();
        $mapBlock = $productPage->getMapBlock();
        //Verification on Product View page
        $this->assertContains(
            $product->getProductMapPrice(),
            $mapBlock->getMapOldPrice(),
            'Displayed on Product page MAP is incorrect'
        );
        $this->assertEquals(
            $product->getProductPrice(),
            $mapBlock->getActualPrice(),
            'Displayed on Product page price is incorrect'
        );
    }

    /**
     * Assert product related data in Shopping Cart
     *
     * @param SimpleProduct $product
     * @return void
     */
    protected function verifyMapInShoppingCart(SimpleProduct $product)
    {
        //Pages
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCartIndex();
        //Steps
        $mapBlock = $productPage->getMapBlock();
        $mapBlock->addToCartFromMap();
        $checkoutCartPage->getMessagesBlock()->assertSuccessMessage();
        //Verification in Shopping Cart
        $unitPrice = $checkoutCartPage->getCartBlock()->getCartItem($product)->getPrice();
        $this->assertEquals(
            $product->getProductPrice(),
            $unitPrice,
            'Incorrect unit price is displayed in Cart'
        );
    }

    /**
     * Disable MAP on Config level
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        $config = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $config->switchData('disable_map_config');
        $config->persist();
    }
}
