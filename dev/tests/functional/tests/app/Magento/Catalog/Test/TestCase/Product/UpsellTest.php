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
use Mtf\Fixture;
use Magento\Catalog\Test\Fixture\Product;
use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Upsell;

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
     * @ZephirId MAGETWO-12391
     */
    public function testCreateUpsell()
    {
        // Precondition: create simple product 1
        $simple1 = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $simple1->switchData('simple');
        $simple1->persist();
        $assignToSimple1 = Factory::getFixtureFactory()->getMagentoCatalogUpsellProducts();
        $assignToSimple1->switchData('add_upsell_products');
        $verify = array($assignToSimple1->getProduct('simple'), $assignToSimple1->getProduct('configurable'));
        //Data
        $productGridPage = Factory::getPageFactory()->getCatalogProductIndex();
        $editProductPage = Factory::getPageFactory()->getCatalogProductEdit();
        //Steps
        $productGridPage->open();
        $productGridPage->getProductGrid()->searchAndOpen(array('sku' => $simple1->getProductSku()));
        $editProductPage->getProductBlockForm()->fill($assignToSimple1);
        $editProductPage->getProductBlockForm()->save($assignToSimple1);
        $editProductPage->getMessagesBlock()->assertSuccessMessage();

        $productGridPage->open();
        $productGridPage->getProductGrid()->searchAndOpen(
            array('sku' => $assignToSimple1->getProduct('configurable')->getProductSku())
        );
        $assignToSimple1->switchData('add_upsell_product');
        $editProductPage->getProductBlockForm()->fill($assignToSimple1);
        $editProductPage->getProductBlockForm()->save($assignToSimple1);
        $editProductPage->getMessagesBlock()->assertSuccessMessage();

        $this->assertOnTheFrontend($simple1, $verify);
    }

    /**
     * @param Product $product
     * @param Product[] $assigned
     */
    protected function assertOnTheFrontEnd(Product $product, $assigned)
    {
        /** @var Product $simple2 */
        /** @var Product $configurable */
        list($simple2, $configurable) = $assigned;
        //Open up simple1 product page
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->init($product);
        $productPage->open();
        $this->assertEquals($product->getProductName(), $productPage->getViewBlock()->getProductName());

        /** @var \Magento\Catalog\Test\Block\Product\ProductList\Upsell $upsellBlock */
        $upsellBlock = $productPage->getUpsellProductBlock();
        //Verify upsell simple2 and configurable on Simple1 product page
        $this->assertTrue($upsellBlock->isUpsellProductVisible($simple2->getProductName()));
        $this->assertTrue($upsellBlock->isUpsellProductVisible($configurable->getProductName()));
        //Open and verify configurable page
        $upsellBlock->openUpsellProduct($configurable->getProductName());
        $this->assertEquals($configurable->getProductName(), $productPage->getViewBlock()->getProductName());
        //Verify upsell simple2 on Configurable product page and open it
        $upsellBlock = $productPage->getUpsellProductBlock();
        $this->assertTrue($upsellBlock->isUpsellProductVisible($simple2->getProductName()));
        $upsellBlock->openUpsellProduct($simple2->getProductName());
        $this->assertEquals($simple2->getProductName(), $productPage->getViewBlock()->getProductName());
        $this->assertFalse($productPage->getUpsellProductBlock()->isVisible());
    }
}
