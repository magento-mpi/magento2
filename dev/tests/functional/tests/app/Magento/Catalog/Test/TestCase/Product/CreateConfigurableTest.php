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

use Magento\Catalog\Test\Fixture\ConfigurableProduct;
use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Mtf\Util\FixtureIterator;

/**
 * Class CreateTest
 * Test product creation
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
     * Test product create
     *
     */
    public function testCreateConfigurableProduct()
    {
        $product = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct()
            ->switchData('configurable_default_category');
        $createProductPage = Factory::getPageFactory()->getAdminCatalogProductNew();
        $manageProductsGrid = Factory::getPageFactory()->getAdminCatalogProductIndex();
        $productBlockForm = $createProductPage->getProductBlockForm();
        $variations = $product->getVariations();
        //Steps
        $manageProductsGrid->open();
        $manageProductsGrid->getProductBlock()->addProduct('configurable');

        $productBlockForm->fill($product);
        $variationsBlock = $createProductPage->getVariationsBlock();
        $variationsBlock->selectAttribute('bb');

        $variationsForm = $createProductPage->getVariationsForm();

        $variationsForm->fillFormPrice($variations);
        $variationsBlock->generateVariations();

        $currentVariations = $createProductPage->getCurrentVariations();
        $currentVariations->fillFormQty($variations);

        $productBlockForm->save($product);
        $affectedAttributeSetChooser = $createProductPage->getAffectedAttributeSetChooser();
        $affectedAttributeSetChooser->chooseNewAndConfirm('attr_set1');
        //Verifying
        $createProductPage->assertProductSaveResult($product);
        $this->assertOnGrid($product);
        $this->assertOnCategory($product);
    }

    /**
     * Assert existing product on admin product grid
     *
     * @param ConfigurableProduct $product
     */
    protected function assertOnGrid($product)
    {
        $productGridPage = Factory::getPageFactory()->getAdminCatalogProductIndex();
        $productGridPage->open();
        //@var Magento\Catalog\Test\Block\Backend\ProductGrid
        $gridBlock = $productGridPage->getProductGrid();
        $sku1 = $product->getProductSku() . '-1';
        $sku2 = $product->getProductSku() . '-2';
        $this->assertTrue($gridBlock->isRowVisible(array('sku' => $product->getProductSku())));
        $this->assertTrue($gridBlock->isRowVisible(array('sku' => $sku1)));
        $this->assertTrue($gridBlock->isRowVisible(array('sku' => $sku2)));
        $this->assertOnCategory($product);
    }

    /**
     * @param ConfigurableProduct $product
     */
    protected function assertOnCategory($product)
    {
        //Pages
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        //Steps
        $categoryPage->openCategory($product->getCategoryName());
        //Verification on category product list
        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertTrue($productListBlock->isProductVisible($product->getProductName()));
        $productListBlock->openProductViewPage($product->getProductName());
        //Verification on product detail page
        $productViewBlock = $productPage->getViewBlock();
        $this->assertEquals($product->getProductName(), $productViewBlock->getProductName());
        $this->assertContains($product->getProductPrice(), $productViewBlock->getProductPrice());
        $array = array('0' => '1 +$1.00', '1' => '2 +$2.00');
        $this->assertEquals($array, $productViewBlock->getProductOptions());
    }
}
