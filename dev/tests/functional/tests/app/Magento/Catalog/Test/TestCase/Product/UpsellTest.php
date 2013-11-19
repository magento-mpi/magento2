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

class UpsellTest extends Functional
{

    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        // Test Case MAGETWO-12391: STEP 1:
        Factory::getApp()->magentoBackendLoginUser();
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
        $product1Fixture->switchData('simple');
        $product1Fixture->persist();

        /* @var Product */
        $product2Fixture = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $product1Fixture->switchData('simple');
        $product2Fixture->persist();

        /* @var ConfigurableProduct */
        $configurableProductFixture = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $product1Fixture->switchData('configurable');
        $configurableProductFixture->persist();

        // Test Steps
        $editProductPage = Factory::getPageFactory()->getCatalogProductEdit();
        $editProductPage->open(array('id' => $product1Fixture->getProductId()));

        // Step 1: (logged into Admin in setup)
        // Step 2: For Simple 1 add as up-sells:- Configurable 1 & Simple 2
        // For Simple 1 add as up-sells:- Configurable 1 & Simple 2

        $this->addUpsellProducts($product1Fixture,
            array($product2Fixture, $configurableProductFixture));

        // Step 3: For Configurable add as up-sells: Simple 2
        // For Simple 1 add as up-sells:- Configurable 1 & Simple 2
        $this->addUpsellProducts($configurableProductFixture, array($product2Fixture));
        // Test on front end that the upsell products are visible.

        // Step 4-7: Go to frontend (implicit)
        $this->checkFrontEnd($product1Fixture, $product2Fixture, $configurableProductFixture);
    }

    /**
     * @param Product $product
     * @param array $upsellProducts
     */
    private function addUpsellProducts($product, $upsellProducts)
    {
        /** @var Product $upsellProduct */
        foreach ($upsellProducts as $upsellProduct) {
            // locate the edit page.
            $productEditPage = Factory::getPageFactory()->getCatalogProductEdit();
            $productEditPage->open(array('id' => $product->getProductId()));
            $productEditPage->getProductBlockForm()
                ->waitForElementVisible('[title="Save"][class*=action]', Locator::SELECTOR_CSS);
            $productEditPage->directToUpsellTab();

            $productEditPage->getProductBlockForm()
                ->waitForElementVisible('[title="Reset Filter"][class*=action]', Locator::SELECTOR_CSS);

            $productEditPage->getProductUpsellGrid()->searchAndSelect(
                array('name' => $upsellProduct->getProductName()));
            $productEditPage->getProductBlockForm()->save($product);
            $productEditPage->getMessagesBlock()->assertSuccessMessage();
        }
    }

    /**
     * Assert input product has proper upsell products in the front end
     *
     * @param Product $product
     * @param Product $upsellSimple2
     * @param Product $upsellConfigurable1
     */
    private function checkFrontEnd($product, $upsellSimple2, $upsellConfigurable1)
    {
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->init($product);
        $productPage->open();

        // check for the upsell products.

        /** @var \Magento\Catalog\Test\Block\Product\Upsell $upsellBlock */
        $upsellBlock = $productPage->getUpsellBlock();
        $this->assertTrue($upsellBlock->isVisible(), "upsell view not found");

        if (!$upsellBlock->verifyProductUpsell($upsellConfigurable1)) {
            $this->fail('Upsell product ' . $upsellConfigurable1->getProductName() .
                ' was not found in the first product page.');
        }

        if (!$upsellBlock->verifyProductUpsell($upsellSimple2)) {
            $this->fail('Upsell product ' . $upsellSimple2->getProductName() .
                ' was not found in the first product page.');
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
}