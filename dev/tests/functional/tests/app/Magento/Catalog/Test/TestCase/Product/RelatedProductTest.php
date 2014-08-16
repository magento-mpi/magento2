<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Catalog\Test\Fixture\Product;
use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Related;

/**
 * Class RelatedProductTest
 * Test promoting products as related
 */
class RelatedProductTest extends Functional
{
    /**
     * Login into backend area before test
     *
     * @return void
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Promoting products as related
     *
     * @ZephyrId MAGETWO-12392
     * @return void
     */
    public function testRelatedProduct()
    {
        // Precondition: create simple product 1
        $simple1 = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $simple1->switchData('simple');
        $simple1->persist();
        $assignToSimple1 = Factory::getFixtureFactory()->getMagentoCatalogRelatedProducts();
        $assignToSimple1->switchData('add_related_products');
        $verify = [$assignToSimple1->getProduct('simple'), $assignToSimple1->getProduct('configurable')];
        //Data
        $productGridPage = Factory::getPageFactory()->getCatalogProductIndex();
        $editProductPage = Factory::getPageFactory()->getCatalogProductEdit();
        //Steps
        $productGridPage->open();
        $productGridPage->getProductGrid()->searchAndOpen(['sku' => $simple1->getProductSku()]);
        $productForm = $editProductPage->getProductForm();
        $productForm->fill($assignToSimple1);
        $editProductPage->getFormPageActions()->save();
        $editProductPage->getMessagesBlock()->assertSuccessMessage();

        $productGridPage->open();
        $productGridPage->getProductGrid()->searchAndOpen(
            ['sku' => $assignToSimple1->getProduct('configurable')->getProductSku()]
        );
        $assignToSimple1->switchData('add_related_product');
        $productForm = $editProductPage->getProductForm();
        $productForm->fill($assignToSimple1);
        $editProductPage->getFormPageActions()->save();
        $editProductPage->getMessagesBlock()->assertSuccessMessage();

        $this->assertOnTheFrontend($simple1, $verify);
    }

    /**
     * Assert configurable product is added to cart together with the proper related product
     *
     * @param Product $product
     * @param Product[] $assigned
     * @return void
     */
    protected function assertOnTheFrontEnd(Product $product, array $assigned)
    {
        /** @var Product $simple2 */
        /** @var Product $configurable */
        list($simple2, $configurable) = $assigned;
        //Open up simple1 product page
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->init($product);
        $productPage->open();
        $this->assertEquals($product->getName(), $productPage->getViewBlock()->getProductName());

        /** @var \Magento\Catalog\Test\Block\Product\ProductList\Related $relatedBlock */
        $relatedBlock = $productPage->getRelatedProductBlock();
        //Verify related simple2 and configurable on Simple1 product page
        $this->assertTrue($relatedBlock->isRelatedProductVisible($simple2->getName()));
        $this->assertTrue($relatedBlock->isRelatedProductSelectable($simple2->getName()));
        $this->assertTrue($relatedBlock->isRelatedProductVisible($configurable->getName()));
        $this->assertFalse($relatedBlock->isRelatedProductSelectable($configurable->getName()));
        //Open and verify configurable page
        $relatedBlock->openRelatedProduct($configurable->getName());
        $this->assertEquals($configurable->getName(), $productPage->getViewBlock()->getProductName());
        //Verify related simple2 on Configurable product page and add to cart it
        $relatedBlock = $productPage->getRelatedProductBlock();
        $this->assertTrue($relatedBlock->isRelatedProductVisible($simple2->getName()));
        $this->assertTrue($relatedBlock->isRelatedProductSelectable($simple2->getName()));
        $relatedBlock->selectProductForAddToCart($simple2->getName());
        $productPage->getViewBlock()->addToCart($configurable);

        //Verify that both configurable product and simple product 2 are added to shopping cart
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartBlock = $checkoutCartPage->getCartBlock();
        $checkoutCartPage->getMessagesBlock()->assertSuccessMessage();
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
