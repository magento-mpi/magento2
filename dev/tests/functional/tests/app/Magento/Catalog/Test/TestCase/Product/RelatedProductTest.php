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
use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Fixture\Product;
use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Related;

/**
 * Class RelatedProductTest
 * Test promoting products as related
 *
 * @package Magento\Catalog\Test\TestCase\Product
 */
class RelatedProductTest extends Functional
{
    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Promoting products as related
     *
     * @ZephyrId MAGETWO-12392
     */
    public function testRelatedProduct()
    {
        // Precondition: create simple product 1
        $simple1 = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $simple1->switchData('simple');
        $simple1->persist();
        $assignToSimple1 = Factory::getFixtureFactory()->getMagentoCatalogRelatedProducts();
        $assignToSimple1->switchData('add_related_products');
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
        $assignToSimple1->switchData('add_related_product');
        $editProductPage->getProductBlockForm()->fill($assignToSimple1);
        $editProductPage->getProductBlockForm()->save($assignToSimple1);
        $editProductPage->getMessagesBlock()->assertSuccessMessage();

        $this->assertOnTheFrontend($simple1, $verify);
    }

    /**
     * Assert configurable product is added to cart together with the proper related product
     *
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

        /** @var \Magento\Catalog\Test\Block\Product\ProductList\Related $relatedBlock */
        $relatedBlock = $productPage->getRelatedProductBlock();
        //Verify related simple2 and configurable on Simple1 product page
        $this->assertTrue($relatedBlock->isRelatedProductVisible($simple2->getProductName()));
        $this->assertTrue($relatedBlock->isRelatedProductSelectable($simple2->getProductName()));
        $this->assertTrue($relatedBlock->isRelatedProductVisible($configurable->getProductName()));
        $this->assertFalse($relatedBlock->isRelatedProductSelectable($configurable->getProductName()));
        //Open and verify configurable page
        $relatedBlock->openRelatedProduct($configurable->getProductName());
        $this->assertEquals($configurable->getProductName(), $productPage->getViewBlock()->getProductName());
        //Verify related simple2 on Configurable product page and add to cart it
        $relatedBlock = $productPage->getRelatedProductBlock();
        $this->assertTrue($relatedBlock->isRelatedProductVisible($simple2->getProductName()));
        $this->assertTrue($relatedBlock->isRelatedProductSelectable($simple2->getProductName()));
        $relatedBlock->selectProductForAddToCart($simple2->getProductName());
        $productPage->getViewBlock()->addToCart($configurable);

        //Verify that both configurable product and simple product 2 are added to shopping cart
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartBlock = $checkoutCartPage->getCartBlock();
        $checkoutCartPage->getMessageBlock()->assertSuccessMessage();
        $this->assertTrue(
            $checkoutCartBlock->isProductInShoppingCart($configurable),
            'Configurable product was not found in the shopping cart.'
        );
        $this->assertTrue(
            $checkoutCartBlock->isProductInShoppingCart($simple2),
            'Related product was not found in the shopping cart.'
        );
    }
}
