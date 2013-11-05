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

/**
 * Class CreateSimpleTest
 * Test simple product and category creation
 *
 * @package Magento\Catalog\Test\TestCase\Product
 */
class CreateSimpleTest extends Functional
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
        //Data
        $product = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $product->switchData('simple_with_new_category');

        //Page & Blocks
        $productListPage = Factory::getPageFactory()->getCatalogProductIndex();
        $productListPage->open();
        $createProductPage = Factory::getPageFactory()->getCatalogProductNew();

        $addProductBlock = $productListPage->getProductBlock();
        $productBlockForm = $createProductPage->getProductBlockForm();

        //Steps
        $addProductBlock->addProduct();
        $productBlockForm->addCategory($product);
        $productBlockForm->fill($product);
        $productBlockForm->save($product);

        $this->assertSuccessMessage("You saved the product.");
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
            sprintf('Message "%s" is not appear', $messageText)
        );
    }

    /**
     * Assert simple product on Frontend
     *
     * @param \Magento\Catalog\Test\Fixture\Product $product
     */
    protected function assertProductOnFrontend(\Magento\Catalog\Test\Fixture\Product $product)
    {
        //Pages
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $productPage = Factory::getPageFactory()->getCatalogProductView();

        $frontendHomePage->open();
        $frontendHomePage->getTopmenu()->selectCategoryByName($product->getNewCategoryName());

        //Verification on category product list
        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertTrue($productListBlock->isProductVisible($product->getProductName()),
            'Product is absent on category page.');

        //Verification on product detail page
        $productViewBlock = $productPage->getViewBlock();
        $productListBlock->openProductViewPage($product->getProductName());
        $this->assertEquals($product->getProductName(), $productViewBlock->getProductName(),
            'Product name does not correspond to specified.');
        $this->assertContains($product->getProductPrice(), $productViewBlock->getProductPrice(),
            'Product price does not correspond to specified.');
        $this->assertContains($product->getProductSku(), $productViewBlock->getProductSku(),
            'Product sku does not correspond to specified.');
    }
}
