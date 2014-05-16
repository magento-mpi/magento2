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
 * Class CreateSimpleWithCategoryTest
 * Test simple product and category creation
 *
 */
class CreateSimpleWithCategoryTest extends Functional
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
     * @ZephyrId MAGETWO-13345
     */
    public function testCreateProduct()
    {
        //Data
        $product = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $product->switchData('simple_with_new_category');

        //Page & Blocks
        $productListPage = Factory::getPageFactory()->getCatalogProductIndex();
        $createProductPage = Factory::getPageFactory()->getCatalogProductNew();
        $addProductBlock = $productListPage->getProductBlock();
        $productBlockForm = $createProductPage->getProductBlockForm();

        //Steps
        $productListPage->open();
        $addProductBlock->addProduct();
        $productBlockForm->addNewCategory($product);
        $productBlockForm->fill($product);
        $productBlockForm->save($product);

        //Verifying
        $this->assertSuccessMessage("You saved the product.");
        //Flush cache
        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();
        $cachePage->getMessagesBlock()->assertSuccessMessage();
        //Verifying
        $this->assertProductOnFrontend($product);
    }

    /**
     * Assert success message
     *
     * @param string $messageText
     */
    protected function assertSuccessMessage($messageText)
    {
        $productEditPage = Factory::getPageFactory()->getCatalogProductEdit();
        $messageBlock = $productEditPage->getMessagesBlock();
        $this->assertContains(
            $messageText,
            $messageBlock->getSuccessMessages(),
            sprintf('Message "%s" is not appear.', $messageText)
        );
    }

    /**
     * Assert simple product on Frontend
     *
     * @param SimpleProduct $product
     */
    protected function assertProductOnFrontend(SimpleProduct $product)
    {
        //Pages
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $productPage = Factory::getPageFactory()->getCatalogProductView();

        $frontendHomePage->open();
        $frontendHomePage->getTopmenu()->selectCategoryByName($product->getNewCategoryName());

        //Verification on category product list
        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertTrue(
            $productListBlock->isProductVisible($product->getProductName()),
            'Product is absent on category page.'
        );

        //Verification on product detail page
        $productViewBlock = $productPage->getViewBlock();
        $productListBlock->openProductViewPage($product->getProductName());
        $this->assertEquals($product->getProductName(), $productViewBlock->getProductName());
        $this->assertEquals($product->getProductPrice(), $productViewBlock->getProductPrice());
    }
}
