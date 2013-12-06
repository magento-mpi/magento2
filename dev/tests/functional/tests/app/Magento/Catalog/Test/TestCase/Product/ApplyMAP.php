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
 * Unassign product from category on Product page
 *
 * @package Magento\Catalog\Test\TestCase\Product
 */
class ApplyMAP extends Functional
{
    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }


    public function testApplyMAPToSimple()
    {
        //Preconditions
        $config = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $config->switchData('enable_map_config');
        $config->persist();
        //Data
        $simple = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $simple->switchData('simple_with_map');
        $simple->persist();
        //Flush cache
        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();
        $cachePage->getMessagesBlock()->assertSuccessMessage();
        //Steps
        $this->verifyMapOnFrontend($simple);
    }

    /**
     * Assert product data on category and product pages
     *
     * @param Product $product
     */
    protected function verifyMapOnFrontend($product)
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
        $productListBlock->openProductViewPage($product->getProductName());
        //Verification on product detail page
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productViewBlock = $productPage->getViewBlock();
        $productViewBlock->openMapBlockOnProductPage();
        $this->assertEquals($product->getProductName(), $productViewBlock->getProductName());
        $this->assertContains($product->getProductMapPrice(), $productViewBlock->getOldPrice());
        $this->assertContains($product->getProductPrice(), $productViewBlock->getActualPrice());
    }
}
