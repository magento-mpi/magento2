<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product\Configurable;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Catalog\Test\Fixture\Product;
use Magento\Catalog\Test\Fixture\ProductAttribute;
use Magento\Catalog\Test\Fixture\ConfigurableProduct;

/**
 * Class CreateWithAttributeTest
 * Configurable product with creating new category and new attribute
 */
class CreateWithAttributeTest extends Functional
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
     * Creating configurable product with creating new category and new attribute (required fields only)
     *
     * @ZephyrId MAGETWO-13361
     * @return void
     */
    public function testCreateConfigurableProductWithNewAttribute()
    {
        //Data
        $product = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $product->switchData('simple_with_new_category');

        $attribute = Factory::getFixtureFactory()->getMagentoCatalogProductAttribute();
        $attribute->switchData('new_attribute');

        $variations = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $variations->switchData('product_variations');
        $variations->provideNewAttributeData($attribute);

        //Steps
        $this->fillSimpleProductWithNewCategory($product);
        $this->addNewAttribute($attribute);
        $this->fillProductVariationsAndSave($variations);

        //Verifying
        $this->assertProductSaved();
        $this->assertOnGrid($product);
        $this->assertOnFrontend($product, $variations);
    }

    /**
     * Fill required fields for simple product with category creation
     *
     * @param Product $product
     * @return void
     */
    protected function fillSimpleProductWithNewCategory($product)
    {
        //Page & Blocks
        $manageProductsGrid = Factory::getPageFactory()->getCatalogProductIndex();
        $createProductPage = Factory::getPageFactory()->getCatalogProductNew();
        $productForm = $createProductPage->getProductForm();

        //Steps
        $manageProductsGrid->open();
        $manageProductsGrid->getGridPageActionBlock()->addProduct();
        $productForm->fill($product);
        $productForm->openTab(Product::GROUP_PRODUCT_DETAILS);
        $productForm->addNewCategory($product);
    }

    /**
     * Add new attribute to product
     *
     * @param ProductAttribute $attribute
     * @return void
     */
    protected function addNewAttribute(ProductAttribute $attribute)
    {
        $createProductPage = Factory::getPageFactory()->getCatalogProductNew();

        $productForm = $createProductPage->getConfigurableProductForm();
        $productForm->openVariationsTab();
        $productForm->clickCreateNewVariationSet();

        $newAttributeForm = $productForm->getConfigurableAttributeEditBlock();
        $this->assertTrue($newAttributeForm->isVisible(), '"New attribute" window is not opened');

        $newAttributeForm->openFrontendProperties();
        $newAttributeForm->fill($attribute);
        $newAttributeForm->saveAttribute();
        Factory::getClientBrowser()->switchToFrame();
    }

    /**
     * Fill product variations and save product
     *
     * @param ConfigurableProduct $variations
     * @return void
     */
    protected function fillProductVariationsAndSave(ConfigurableProduct $variations)
    {
        $createProductPage = Factory::getPageFactory()->getCatalogProductNew();
        $createProductPage->getProductForm()->fillVariations($variations);
        $createProductPage->getFormAction()->saveProduct($createProductPage, $variations);
    }

    /**
     * Assert product was saved
     *
     * @return void
     */
    protected function assertProductSaved()
    {
        $createProductPage = Factory::getPageFactory()->getCatalogProductNew();
        $this->assertEquals(
            'You saved the product.',
            $createProductPage->getMessagesBlock()->getSuccessMessages(),
            'Product was not saved'
        );
    }

    /**
     * Assert existing product on backend product grid
     *
     * @param Product $product
     * @return void
     */
    protected function assertOnGrid(Product $product)
    {
        $configurableSearch = array(
            'sku' => $product->getProductSku(),
            'type' => 'Configurable Product',
        );
        $productGridPage = Factory::getPageFactory()->getCatalogProductIndex();
        $productGridPage->open();
        $gridBlock = $productGridPage->getProductGrid();
        $this->assertTrue($gridBlock->isRowVisible($configurableSearch), 'Configurable product was not found.');
    }

    /**
     * Assert configurable product on frontend
     *
     * @param Product $product
     * @param ConfigurableProduct $variations
     * @return void
     */
    protected function assertOnFrontend(Product $product, ConfigurableProduct $variations)
    {
        //Pages
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        //Steps
        $frontendHomePage->open();
        $frontendHomePage->getTopmenu()->selectCategoryByName($product->getNewCategoryName());
        //Verification on category product list
        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertTrue(
            $productListBlock->isProductVisible($product->getName()),
            'Product is absent on category page.'
        );
        //Verification on product detail page
        $productViewBlock = $productPage->getViewBlock();
        $productListBlock->openProductViewPage($product->getName());
        $this->assertEquals(
            $product->getName(),
            $productViewBlock->getProductName(),
            'Product name does not correspond to specified.'
        );
        $price = $productViewBlock->getProductPrice();
        $this->assertEquals(
            number_format($product->getProductPrice(), 2),
            $price['price_regular_price'],
            'Product price does not correspond to specified.'
        );
        $this->assertTrue(
            $productViewBlock->verifyProductOptions($variations),
            'Added configurable options are absent.'
        );
    }
}
