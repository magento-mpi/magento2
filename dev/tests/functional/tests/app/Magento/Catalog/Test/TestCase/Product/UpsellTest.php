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

use Magento\Catalog\Test\Page\Product\CatalogProductEdit;
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
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Product Up-selling.  Assign upselling to products and see them related on the front-end.
     *
     * @ZephirId MAGEGTWO-12391
     */
    public function testCreateUpsell()
    {
        $product1Fixture = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $product1Fixture->switchData('simple');
        $product1Fixture->persist();

        $product2Fixture = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $product2Fixture->switchData('simple');
        $product2Fixture->persist();

        $configurableProductFixture = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $configurableProductFixture->persist();

        // Test Steps
        $editProductPage = Factory::getPageFactory()->getCatalogProductEdit();
        $editProductPage->open(array('id' => $product1Fixture->getProductId()));

        // Step 1: (logged into Admin in setup)
        // Step 2: For Simple 1 add as up-sells:- Configurable 1 & Simple 2
        // For Simple 1 add as up-sells:- Configurable 1 & Simple 2

        $this->addUpsellProducts($product1Fixture, array($product2Fixture, $configurableProductFixture));

        // Step 3: For Configurable add as up-sells: Simple 2
        // For Simple 1 add as up-sells:- Configurable 1 & Simple 2
        $this->addUpsellProducts($configurableProductFixture, array($product2Fixture));
        // Test on front end that the upsell products are visible.

        // Step 4 Go to front end.
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->init($product1Fixture);
        $productPage->open();

        // Step 5: check for the upsell products.

        /** @var \Magento\Catalog\Test\Block\Product\ProductList\Upsell $upsellBlock */
        $upsellBlock = $productPage->getUpsellBlock();
        $this->assertTrue($upsellBlock->isVisible(), "upsell view not found");

        $this->assertTrue($upsellBlock->verifyProductUpsell($configurableProductFixture),
            'Upsell product ' . $configurableProductFixture->getProductName() .
            ' was not found in the first product page.');

        $this->assertTrue($upsellBlock->verifyProductUpsell($product2Fixture),
            'Upsell product ' . $product2Fixture->getProductName() .
            ' was not found in the first product page.');

        // Step 6.  Click on the configurable 1 product.
        // See that the Simple 2 is in the upsells.

        $upsellBlock->clickLink($configurableProductFixture);

        // load the block
        $upsellBlock = $productPage->getUpsellBlock();
        $this->assertTrue($upsellBlock->isVisible(), "Upsell view not found");

        $this->assertTrue($upsellBlock->verifyProductUpsell($product2Fixture),
            $product2Fixture->getProductName() . " not found on configurable page.");

        // Step 7.  Click on the simple 2 product.
        // See that no upsells are present.

        $upsellBlock->clickLink($product2Fixture);

        // load the new block
        $upsellBlock = $productPage->getUpsellBlock();
        $this->assertFalse($upsellBlock->isVisible(), "Usell view should not be visible");

    }

    /**
     * Assign an array of products as upsells to the passed in $product
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
            $productBlock = $productEditPage->getProductBlockForm();
            $productBlock->showAdvanced();
            //$productBlock->waitForElementVisible('[title="Save"][class*=action]', Locator::SELECTOR_CSS);
            $productEditPage->getProductBlockForm()->openUpsellTab();
            $productEditPage->getProductUpsellGrid()->searchAndSelect(array('name' => $upsellProduct->getProductName()));
            $productBlock->save($product);
            $productEditPage->getMessagesBlock()->assertSuccessMessage();
        }
    }
}
