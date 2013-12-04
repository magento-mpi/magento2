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

use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Catalog\Test\Fixture\SimpleProduct;

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
        $simple1 = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $simple1->switchData('simple');
        $simple1->persist();

        $simple2 = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $simple2->switchData('simple');
        $simple2->persist();

        $configurable = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $configurable->switchData('configurable');
        $configurable->persist();

        // Test Steps
        $editProductPage = Factory::getPageFactory()->getCatalogProductEdit();
        $editProductPage->open(array('id' => $simple1->getProductId()));

        // Step 1: (logged into Admin in setup)
        // Step 2: For Simple 1 add as up-sells:- Configurable 1 & Simple 2
        // For Simple 1 add as up-sells:- Configurable 1 & Simple 2

        $this->addUpsellProducts($simple1, array($simple2, $configurable));

        // Step 3: For Configurable add as up-sells: Simple 2
        // For Simple 1 add as up-sells:- Configurable 1 & Simple 2
        $this->addUpsellProducts($configurable, array($simple2));
        // Test on front end that the upsell products are visible.

        // Step 4 Go to front end.
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->init($simple1);
        $productPage->open();

        // Step 5: check for the upsell products.

        /** @var \Magento\Catalog\Test\Block\Product\ProductList\Upsell $upsellBlock */
        $upsellBlock = $productPage->getUpsellBlock();
        $this->assertTrue($upsellBlock->isVisible(), "upsell view not found");

        $this->assertTrue($upsellBlock->verifyProductUpsell($configurable),
            'Upsell product ' . $configurable->getProductName() .
            ' was not found in the first product page.');

        $this->assertTrue($upsellBlock->verifyProductUpsell($simple2),
            'Upsell product ' . $simple2->getProductName() .
            ' was not found in the first product page.');

        // Step 6.  Click on the configurable 1 product.
        // See that the Simple 2 is in the upsells.

        $upsellBlock->clickLink($configurable);

        // load the block
        $upsellBlock = $productPage->getUpsellBlock();
        $this->assertTrue($upsellBlock->isVisible(), "Upsell view not found");

        $this->assertTrue($upsellBlock->verifyProductUpsell($simple2),
            $simple2->getProductName() . " not found on configurable page.");

        // Step 7.  Click on the simple 2 product.
        // See that no upsells are present.

        $upsellBlock->clickLink($simple2);

        // load the new block
        $upsellBlock = $productPage->getUpsellBlock();
        $this->assertFalse($upsellBlock->isVisible(), "Upsell view should not be visible");

    }

    /**
     * Assign an array of products as upsells to the passed in $product
     * @param SimpleProduct $product
     * @param array $upsellProducts
     */
    private function addUpsellProducts($product, $upsellProducts)
    {
        /** @var SimpleProduct $upsellProduct */
        foreach ($upsellProducts as $upsellProduct) {
            // locate the edit page.
            $productEditPage = Factory::getPageFactory()->getCatalogProductEdit();
            $productEditPage->open(array('id' => $product->getProductId()));
            $productBlock = $productEditPage->getProductBlockForm();
            $productBlock->showAdvanced();
            //$productBlock->waitForElementVisible('[title="Save"][class*=action]', Locator::SELECTOR_CSS);
            $productEditPage->getProductBlockForm()->openUpsellTab();
            $productEditPage->getUpsellBlock()->searchAndSelect(array('name' => $upsellProduct->getProductName()));
            $productBlock->save($product);
            $productEditPage->getMessagesBlock()->assertSuccessMessage();
        }
    }
}
