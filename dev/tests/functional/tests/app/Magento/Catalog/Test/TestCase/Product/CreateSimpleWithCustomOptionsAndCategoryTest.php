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
use Magento\Catalog\Test\Fixture\Product;

/**
 * Class CreateSimpleWithCustomOptionsAndCategoryTest
 * Create simple product with custom options
 */
class CreateSimpleWithCustomOptionsAndCategoryTest extends Functional
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
     * Creating simple product with custom options and assigning it to the category
     *
     * @ZephyrId MAGETWO-12703
     * @return void
     */
    public function testCreateProduct()
    {
        $product = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $product->switchData('simple_custom_options');
        //Data
        $createProductPage = Factory::getPageFactory()->getCatalogProductNew();
        $createProductPage->init($product);
        $productForm = $createProductPage->getForm();
        //Steps
        $createProductPage->open();
        $productForm->fillProduct($product);
        $createProductPage->getFormAction()->save();
        //Verifying
        $createProductPage->getMessagesBlock()->assertSuccessMessage();
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
     * @return void
     */
    protected function assertOnGrid($product)
    {
        $productGridPage = Factory::getPageFactory()->getCatalogProductIndex();
        $productGridPage->open();
        /** @var \Magento\Catalog\Test\Block\Adminhtml\Product\Grid $gridBlock */
        $gridBlock = $productGridPage->getProductGrid();
        $this->assertTrue($gridBlock->isRowVisible(array('sku' => $product->getProductSku())));
    }

    /**
     * Assert product data on category and product pages
     *
     * @param Product $product
     * @return void
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
        $price = $productViewBlock->getProductPrice();
        $this->assertEquals(number_format($product->getProductPrice(), 2), $price['price_regular_price']);

        $productOptionsBlock = $productPage->getCustomOptionsBlock();
        $fixture = $product->getData('fields/custom_options/value');
        $actualOptions = $productOptionsBlock->getOptions();
        $this->assertCount(count($fixture), $actualOptions);
        $this->assertTrue(isset($actualOptions[$fixture[0]['title']]['title']));
        $this->assertEquals($fixture[0]['title'], $actualOptions[$fixture[0]['title']]['title']);
    }
}
