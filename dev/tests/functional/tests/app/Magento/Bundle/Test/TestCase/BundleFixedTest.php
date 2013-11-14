<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Catalog\Test\Fixture\Product;
use Magento\Bundle\Test\Fixture\Bundle;

/**
 * Class BundleTest
 * Bundle product creation tests
 *
 * @package Magento\Bundle\Test\TestCase
 */
class BundleFixedTest extends Functional
{
    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Create bundle
     */
    public function testCreate()
    {
        //Data
        $bundle = Factory::getFixtureFactory()->getMagentoBundleBundleFixed();
        $bundle->switchData('bundle_fixed');
        //Pages & Blocks
        $manageProductsGrid = Factory::getPageFactory()->getCatalogProductIndex();
        $createProductPage = Factory::getPageFactory()->getCatalogProductNew();
        $productBlockForm = $createProductPage->getProductBlockForm();
        //Steps
        $manageProductsGrid->open();
        $manageProductsGrid->getProductBlock()->addProduct('bundle');
        $productBlockForm->fill($bundle);
        $productBlockForm->save($bundle);
        //Verification
        $createProductPage->getMessagesBlock()->assertSuccessMessage();
        // Flush cache
        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();
        //Verification
        $this->assertOnGrid($bundle);
        $this->assertOnCategory($bundle);
    }

    /**
     * Assert existing product on admin product grid
     *
     * @param Product $product
     */
    protected function assertOnGrid($product)
    {
        $productGridPage = Factory::getPageFactory()->getCatalogProductIndex();
        $productGridPage->open();
        //@var Magento\Catalog\Test\Block\Backend\ProductGrid
        $gridBlock = $productGridPage->getProductGrid();
        $this->assertTrue($gridBlock->isRowVisible(array('sku' => $product->getProductSku())));
    }

    /**
     * @param Bundle $product
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
        //Verification on category product list
        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertTrue($productListBlock->isProductVisible($product->getProductName()));
        $productListBlock->openProductViewPage($product->getProductName());
        //Verification on product detail page
        $productViewBlock = $productPage->getViewBlock();
        $this->assertEquals($product->getProductName(), $productViewBlock->getProductName());

        $actualPrices = $productViewBlock->getProductPrice();
        $expectedPrices = $product->getProductPrice();
        foreach ($actualPrices as $priceType => $actualPrice) {
            $this->assertContains($expectedPrices[$priceType], $actualPrice);
        }

        // @TODO: add click on "Customize and Add To Cart" button and assert options count
        $productOptionsBlock = $productPage->getOptionsBlock();
        $actualOptions = $productOptionsBlock->getBundleOptions();
        $expectedOptions = $product->getBundleOptions();
        foreach ($actualOptions as $optionType => $actualOption) {
            $this->assertContains($expectedOptions[$optionType], $actualOption);
        }
    }
}
