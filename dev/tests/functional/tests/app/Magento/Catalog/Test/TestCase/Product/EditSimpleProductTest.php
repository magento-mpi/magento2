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
 * Edit products
 *
 */
class EditSimpleProductTest extends Functional
{
    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Edit simple product
     *
     * @ZephyrId MAGETWO-12428
     */
    public function testEditProduct()
    {
        $product = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $product->switchData('simple');
        $product->persist();
        $editProduct = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $editProduct->switchData('simple_edit_required_fields');

        $productGridPage = Factory::getPageFactory()->getCatalogProductIndex();
        $gridBlock = $productGridPage->getProductGrid();
        $editProductPage = Factory::getPageFactory()->getCatalogProductEdit();
        $productBlockForm = $editProductPage->getProductBlockForm();
        $cachePage = Factory::getPageFactory()->getAdminCache();

        $productGridPage->open();
        $gridBlock->searchAndOpen(array(
            'sku' => $product->getProductSku(),
            'type' => 'Simple Product'
        ));
        $productBlockForm->fill($editProduct);
        $productBlockForm->save($editProduct);
        //Verifying
        $editProductPage->getMessagesBlock()->assertSuccessMessage();
        // Flush cache
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();
        //Verifying
        $this->assertOnGrid($editProduct);
        $this->assertOnCategoryPage($editProduct, $product->getCategoryName());
        $this->assertOnProductPage($product, $editProduct);
    }

    /**
     * Assert existing product on admin product grid
     *
     * @param SimpleProduct $product
     */
    protected function assertOnGrid($product)
    {
        $productGridPage = Factory::getPageFactory()->getCatalogProductIndex();
        $productGridPage->open();
        $gridBlock = $productGridPage->getProductGrid();
        $this->assertTrue($gridBlock->isRowVisible(array('sku' => $product->getProductSku())));
    }

    /**
     * Assert product data on category page
     *
     * @param SimpleProduct $product
     * @param string $categoryName
     */
    protected function assertOnCategoryPage($product, $categoryName)
    {
        //Pages
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        //Steps
        $frontendHomePage->open();
        $frontendHomePage->getTopmenu()->selectCategoryByName($categoryName);
        //Verification on category product list
        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertTrue($productListBlock->isProductVisible($product->getProductName()));
    }

    /**
     * Assert product data on product page
     *
     * @param SimpleProduct $productOld
     * @param SimpleProduct $productEdited
     */
    protected function assertOnProductPage($productOld, $productEdited)
    {
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->init($productOld);
        $productPage->open();

        $productViewBlock = $productPage->getViewBlock();
        $this->assertEquals($productEdited->getProductName(), $productViewBlock->getProductName());
        $this->assertEquals($productEdited->getProductPrice(), $productViewBlock->getProductPrice());
    }
}
