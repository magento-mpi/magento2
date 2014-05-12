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
use Magento\Catalog\Test\Fixture\GroupedProduct;

/**
 * Class CreateGroupedTest
 * Grouped product
 *
 */
class CreateGroupedTest extends Functional
{
    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Creating Grouped product and assigning it to category
     *
     * @ZephyrId MAGETWO-13610
     */
    public function testCreateGroupedProduct()
    {
        //Data
        $product = Factory::getFixtureFactory()->getMagentoCatalogGroupedProduct();
        $product->switchData('grouped');
        //Page & Blocks
        $manageProductsGrid = Factory::getPageFactory()->getCatalogProductIndex();
        $createProductPage = Factory::getPageFactory()->getCatalogProductNew();
        $productBlockForm = $createProductPage->getProductBlockForm();
        //Steps
        $manageProductsGrid->open();
        $manageProductsGrid->getProductBlock()->addProduct('grouped');
        $productBlockForm->fill($product);
        $productBlockForm->save($product);
        //Verifying
        $createProductPage->getMessagesBlock()->assertSuccessMessage();
        // Flush cache
        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();
        //Verifying
        $this->assertOnGrid($product);
        $this->assertOnFrontend($product);
    }

    /**
     * Assert existing product on admin product grid
     *
     * @param GroupedProduct $product
     */
    protected function assertOnGrid($product)
    {
        //Search data
        $search = array(
            'sku' => $product->getProductSku(),
            'type' => 'Grouped Product'
        );
        //Page & Block
        $productGridPage = Factory::getPageFactory()->getCatalogProductIndex();
        $productGridPage->open();
        /** @var \Magento\Catalog\Test\Block\Backend\ProductGrid */
        $gridBlock = $productGridPage->getProductGrid();
        //Assertion
        $this->assertTrue($gridBlock->isRowVisible($search), 'Grouped product was not found.');
    }

    /**
     * Assert Grouped product on Frontend
     *
     * @param GroupedProduct $product
     */
    protected function assertOnFrontend(GroupedProduct $product)
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
        $this->assertTrue(
            $productListBlock->isProductVisible($product->getProductName()),
            'Product is absent on category page.'
        );
        //Verification on product detail page
        $productViewBlock = $productPage->getViewBlock();
        $productListBlock->openProductViewPage($product->getProductName());
        $this->assertEquals(
            $product->getProductName(),
            $productViewBlock->getProductName(),
            'Product name does not correspond to specified.'
        );
        $this->assertTrue($productViewBlock->verifyGroupedProducts($product), 'Added Grouped options are absent');
    }
}
