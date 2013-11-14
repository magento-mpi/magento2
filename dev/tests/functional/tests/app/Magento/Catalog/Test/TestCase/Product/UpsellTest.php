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
use Magento\Catalog\Test\Fixture\Product;
use Magento\Catalog\Test\Fixture\ConfigurableProduct;

class UpsellTest extends Functional {

    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    protected function createConfigurable() {
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
        return $product;
    }

    protected function createSimple() {
        $product = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $product->switchData('simple');
        //Data
        $createProductPage = Factory::getPageFactory()->getCatalogProductNew();
        $createProductPage->init($product);
        $productBlockForm = $createProductPage->getProductBlockForm();
        //Steps
        $createProductPage->open();
        $productBlockForm->fill($product);
        $productBlockForm->save($product);
        return $product;
    }

    /**
     * Product Up-selling.  Assign upselling to products and see them related on the front-end.
     *
     * @ZephirId MAGEGTWO-12391
     */
    public function testCreateUpsell()
    {
        //@var Product
        $product1Fixture = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $product1Fixture->persist();

        //@var Product
        $product2Fixture = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $product2Fixture->persist();

        //@var ConfigurableProduct
        $configurableFixture = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $configurableFixture->persist();


        // Test Steps
        //Page
        // For Simple 1 add as up-sells:- Configurable 1 & Simple 2
        $productGridPage = Factory::getPageFactory()->getCatalogProductIndex();
        $productGridPage->open();
        $gridBlock = $productGridPage->getProductGrid();

        $productGridPage->getProductGrid()->searchAndOpen(array('sku' => $product1Fixture->getProductSku()));
        //$productGridPage->editProduct(array('id' => $product1Fixture->getProductId()));

/*        $product = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $product->switchData('simple');
        //Data
        $createProductPage = Factory::getPageFactory()->getCatalogProductNew();
        $createProductPage->init($product);
        $productBlockForm = $createProductPage->getProductBlockForm();
        //Steps
        $createProductPage->open();
        $productBlockForm->fill($product);
        $productBlockForm->save($product);
        //Verifying
        $createProductPage->getMessagesBlock()->assertSuccessMessage();
*/
        //
        // For Configurable 1 add as up-sells: Simple 2

        $this->markTestIncomplete('MAGETWO-15966');
    }

}