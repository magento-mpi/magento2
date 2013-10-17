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
use Mtf\Util\FixtureIterator;
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
     *
     * @param Product $product
     * @dataProvider dataProviderTestCreateProduct
     */
    public function testCreateProduct(Product $product)
    {
        //Create Category
        $category = Factory::getFixtureFactory()->getMagentoCatalogCategory();
        $category->persist();
        //Data
        $createProductPage = Factory::getPageFactory()->getAdminCatalogProductNew();
        $createProductPage->init($product);
        $productBlockForm = $createProductPage->getProductBlockForm();
        //Steps
        $createProductPage->open();
        $productBlockForm->fill($product);
        $productBlockForm->fillCategory($category->getCategoryName());
        $productBlockForm->save($product);
        //Verifying
        $createProductPage->assertProductSaveResult($product);
        $this->assertOnGrid($product);
        $this->assertOnCategory($category, $product);
    }

    /**
     * @return Product
     */
    public function dataProviderTestCreateProduct()
    {
        return new FixtureIterator(Factory::getFixtureFactory()->getMagentoCatalogProduct());
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
        $this->assertTrue($gridBlock->isProductExist($product->getProductSku()));
    }

    /**
     * @param Fixture $category
     * @param Product $product
     */
    protected function assertOnCategory($category, $product)
    {
        //Pages
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        //Steps
        $categoryPage->init($category);
        $categoryPage->open();
        //Verification on category product list
        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertTrue($productListBlock->isProductVisible($product->getProductName()));
        $productListBlock->openProductViewPage($product->getProductName());
        //Verification on product detail page
        $productViewBlock = $productPage->getViewBlock();
        $this->assertEquals($product->getProductName(), $productViewBlock->getProductName());
        $this->assertContains($product->getProductPrice(), $productViewBlock->getProductPrice());
    }
}
