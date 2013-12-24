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
use Magento\Catalog\Test\Fixture\ConfigurableProduct;

/**
 * Class CreateConfigurableTest
 * Configurable product
 *
 * @package Magento\Catalog\Test\TestCase\Product
 */
class CreateConfigurableTest extends Functional
{
    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Creating configurable product and assigning it to category
     *
     * @ZephyrId MAGETWO-12620
     */
    public function testCreateConfigurableProduct()
    {
        //Data
        $product = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $product->switchData('configurable');
        //Page & Blocks
        $manageProductsGrid = Factory::getPageFactory()->getCatalogProductIndex();
        $createProductPage = Factory::getPageFactory()->getCatalogProductNew();
        $productBlockForm = $createProductPage->getProductBlockForm();
        //Steps
        $manageProductsGrid->open();
        $manageProductsGrid->getProductBlock()->addProduct('configurable');
        $productBlockForm->fill($product);
        $productBlockForm->save($product);
        //Verifying
        $createProductPage->getMessagesBlock()->assertSuccessMessage();
        //Flush cache
        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();
        $cachePage->getMessagesBlock()->assertSuccessMessage();
        //Verifying
        $this->assertOnGrid($product);
        $this->assertOnFrontend($product);
    }

    /**
     * Assert existing product on admin product grid
     *
     * @param ConfigurableProduct $product
     */
    protected function assertOnGrid($product)
    {
        //Search data
        $configurableSearch = array(
            'sku' => $product->getProductSku(),
            'type' => 'Configurable Product'
        );
        $variationSkus = $product->getVariationSkus();
        //Page & Block
        $productGridPage = Factory::getPageFactory()->getCatalogProductIndex();
        $productGridPage->open();
        /** @var \Magento\Catalog\Test\Block\Backend\ProductGrid */
        $gridBlock = $productGridPage->getProductGrid();
        //Assertion
        $this->assertTrue($gridBlock->isRowVisible($configurableSearch), 'Configurable product was not found.');
        foreach ($variationSkus as $sku) {
            $this->assertTrue(
                $gridBlock->isRowVisible(array('sku' => $sku, 'type' => 'Simple Product')),
                'Variation with sku "' . $sku . '" was not found.'
            );
        }
    }

    /**
     * Assert configurable product on Frontend
     *
     * @param ConfigurableProduct $product
     */
    protected function assertOnFrontend(ConfigurableProduct $product)
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
        $this->assertTrue($productListBlock->isProductVisible($product->getProductName()),
            'Product is absent on category page.');
        //Verification on product detail page
        $productViewBlock = $productPage->getViewBlock();
        $productListBlock->openProductViewPage($product->getProductName());
        $this->assertEquals($product->getProductName(), $productViewBlock->getProductName(),
            'Product name does not correspond to specified.');
        $this->assertEquals($product->getProductPrice(), $productViewBlock->getProductPrice(),
            'Product price does not correspond to specified.');
        $this->assertTrue($productViewBlock->verifyProductOptions($product), 'Added configurable options are absent');
    }
}
