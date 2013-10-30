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
 * Class CreateTest
 * Test product creation
 *
 * @package Magento\Catalog\Test\TestCase\Product
 */
class CreateTest extends Functional
{
    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Test product create
     */
    public function testCreateProduct()
    {
        $product = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $product->switchData('simple_with_category');
        //Data
        $createProductPage = Factory::getPageFactory()->getAdminCatalogProductNew();
        $createProductPage->init($product);
        $productBlockForm = $createProductPage->getProductBlockForm();
        //Steps
        $createProductPage->open();
        $productBlockForm->fill($product);
        $productBlockForm->save($product);
        $createProductPage->assertProductSaveResult($product);
        // Flush cache
        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();
        //Verifying
        $this->assertOnGrid($product);
        $this->assertOnCategory($product);
    }

    /**
     * Assert existing product on admin product grid
     *
     * @param Product $product
     */
    protected function assertOnGrid($product)
    {
        $productGridPage = Factory::getPageFactory()->getAdminCatalogProductIndex();
        $productGridPage->open();
        //@var Magento\Catalog\Test\Block\Backend\ProductGrid
        $gridBlock = $productGridPage->getProductGrid();
        $this->assertTrue($gridBlock->isRowVisible(array('sku' => $product->getProductSku())));
    }

    /**
     * @param Product $product
     */
    protected function assertOnCategory($product)
    {
        //Steps
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $categoryPage->openCategory($product->getCategoryName());
        //Verification on category product list
        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertTrue($productListBlock->isProductVisible($product->getProductName()));
        $productListBlock->openProductViewPage($product->getProductName());
        //Verification on product detail page
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productViewBlock = $productPage->getViewBlock();
        $this->assertEquals($product->getProductName(), $productViewBlock->getProductName());
        $this->assertContains($product->getProductPrice(), $productViewBlock->getProductPrice());
    }
}
