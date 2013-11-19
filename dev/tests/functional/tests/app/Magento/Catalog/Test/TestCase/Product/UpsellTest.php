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
use Mtf\Client\Element;
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
        // Test Case MAGETWO-12391: STEP 1:
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
     * Assert input product has proper upsell products in the front end
     *
     * @param \Magento\Catalog\Test\Fixture\Product $product
     * @param \Magento\Catalog\Test\Fixture\Product $upsellSimple2
     * @param \Magento\Catalog\Test\Fixture\Product $upsellConfigurable1
     */
    protected function checkFrontEnd($product, $upsellSimple2, $upsellConfigurable1)
    {
        //Pages
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $resultPage = Factory::getPageFactory()->getCatalogsearchResult();
        $productPage = Factory::getPageFactory()->getCatalogProductView();

        //Blocks
        $productViewBlock = $productPage->getViewBlock();

        $productListBlock = $resultPage->getListProductBlock();

        //Steps
        $frontendHomePage->open();
        $frontendHomePage->getSearchBlock()->search($product->getProductSku());

        //Verifying

        // Step 5 - Go to Simple 1 page
        // Simple 1 page is opened,  Product page has an Up-sells section :
        // "You may also be interested in the following product(s)" with  both - Configurable 1 and - Simple 2

        $this->assertTrue($productListBlock->isProductVisible($product->getProductName()),
            'Product was not found.');
        $productListBlock->openProductViewPage($product->getProductName());
        $this->assertEquals($product->getProductName(), $productViewBlock->getProductName(),
            'Wrong product page has been opened.');

        // check for the upsell products.

        /** @var \Magento\Catalog\Test\Block\Product\Upsell $upsellBlock */
        $upsellBlock = $productPage->getUpsellBlock();
        $this->assertTrue($upsellBlock->isVisible(), "upsell view not found");

        if (!$upsellBlock->verifyProductUpsell($upsellConfigurable1) ){
            $this->fail('Upsell product ' . $upsellConfigurable1->getProductName() .  ' was not found in the first product page.');
        }

        if (!$upsellBlock->verifyProductUpsell($upsellSimple2) ){
            $this->fail('Upsell product ' . $upsellSimple2->getProductName() .  ' was not found in the first product page.');
        }

        // Step 6.  Click on the configurable 1 product.
        // See that the Simple 2 is in the upsells.

        $upsellBlock->clickLink($upsellConfigurable1);

        // load the new product page
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $upsellBlock = $productPage->getUpsellBlock();
        $this->assertTrue($upsellBlock->isVisible(), "upsell view not found");

        $this->assertTrue($upsellBlock->verifyProductUpsell($upsellSimple2),
            $upsellSimple2->getProductName() . " not found on configurable page.");

        // Step 7.  Click on the simple 2 product.
        // See that no upsells are present.

        $upsellBlock->clickLink($upsellSimple2);

        // load the new product page
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $upsellBlock = $productPage->getUpsellBlock();
        $this->assertFalse($upsellBlock->isVisible(), "upsell view should not be visible");

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
        $this->assertNotEmpty($product1Fixture->getProductId(), "no product id!");

        /* @var Product */
        $product2Fixture = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $product2Fixture->persist();

        /* @var ConfigurableProduct */
        $configurableProductFixture = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $configurableProductFixture->persist();
        $this->assertNotEmpty($configurableProductFixture->getProductId(), "no product id!");

        // Test Steps
        $editProductPage = Factory::getPageFactory()->getCatalogProductEdit();
        $editProductPage->open(array('id' => $product1Fixture->getProductId()));

        /* TODO: find a way to better way to access the upsell tab.  Right now they are only accessed when
           filling in a form, based on the fixture.  But Upsell tab cannot be filled in by the fixture,
          and instead uses search and select. */
        //$upsell = new Upsell($editProductPage->getProductBlockForm()->getRootElement());

        // Step 1: (logged into Admin in setup)
        // Step 2: For Simple 1 add as up-sells:- Configurable 1 & Simple 2
        // For Simple 1 add as up-sells:- Configurable 1 & Simple 2

        Upsell::addUpsellProducts($product1Fixture,
            array($product2Fixture, $configurableProductFixture));

        $this->assertNotEmpty($product1Fixture->getProductId(), "no product id!");

        // Step 3: For Configurable add as up-sells: Simple 2
        // For Simple 1 add as up-sells:- Configurable 1 & Simple 2

        Upsell::addUpsellProducts($configurableProductFixture, array($product2Fixture));
        // Test on front end that the upsell products are visible.

        // Step 4-7: Go to frontend (implicit)
        $this->checkFrontEnd($product1Fixture, $product2Fixture, $configurableProductFixture);
   }
}