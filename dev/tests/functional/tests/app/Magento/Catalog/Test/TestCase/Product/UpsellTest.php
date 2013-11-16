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

use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Catalog\Test\Fixture\Product;
use Magento\Catalog\Test\Fixture\ConfigurableProduct;
use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Upsell;
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

        /* @var Product */
        $product1Fixture = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $product1Fixture->persist();

        /* @var Product */
        $product2Fixture = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $product2Fixture->persist();

        /* @var ConfigurableProduct */
        $configurableProductFixture = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $configurableProductFixture->persist();


        // Flush cache
        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();

        // Test Steps
        $editProductPage = Factory::getPageFactory()->getCatalogProductEdit();
        $editProductPage->open(array('id' => $product1Fixture->getProductId()));

        /* TODO: find a way to better way to access the upsell tab.  Right now they are only accessed when
           filling in a form, based on the fixture.  But Upsell tab cannot be filled in by the fixture,
          and instead uses search and select. */
        $upsell = new Upsell($editProductPage->getProductBlockForm()->getRootElement());

        // Step 1:
        // For Simple 1 add as up-sells:- Configurable 1 & Simple 2
        $upsell->addUpsellProducts($product1Fixture, array($configurableProductFixture));

        //TODO:  2 to more products results in failure, currently.
        //$upsell->addUpsellProducts($product2Fixture, array($product1Fixture, $configurableProductFixture));


        $this->markTestIncomplete('MAGETWO-15966');
    }
}