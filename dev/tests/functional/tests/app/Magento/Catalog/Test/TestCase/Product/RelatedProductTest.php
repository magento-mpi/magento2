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
use Magento\Catalog\Test\Fixture\SimpleProduct;
use Magento\Catalog\Test\Fixture\ConfigurableProduct;

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
        $simpleProduct1 = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $simpleProduct1->switchData('simple');
        $simpleProduct1->persist();
        // Precondition: create simple product 2
        $simpleProduct2 = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $simpleProduct2->switchData('simple');
        $simpleProduct2->persist();
        // Precondition: create configurable product
        $configurableProduct = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $configurableProduct->switchData('configurable');
        $configurableProduct->persist();

        $this->addRelatedProduct($simpleProduct1, array($simpleProduct2, $configurableProduct));
        $this->addRelatedProduct($configurableProduct, array($simpleProduct2));
        $this->assertOnTheFrontend($simpleProduct1, $simpleProduct2, $configurableProduct);
    }

    /**
     * Configure related products in the backend
     *
     * @param Product $product
     * @param Product[] $relatedProducts
     */
    private function addRelatedProduct($product, $relatedProducts)
    {
        $productGridPage = Factory::getPageFactory()->getCatalogProductIndex();
        $productEditPage = Factory::getPageFactory()->getCatalogProductEdit();

        $gridBlock = $productGridPage->getProductGrid();
        $productBlock = $productEditPage->getProductBlockForm();

        $productGridPage->open();
        $gridBlock->searchAndOpen(array('sku' => $product->getProductSku()));
        $productBlock->openRelatedProductTab();
        /** @var Product $relatedProduct */
        foreach ($relatedProducts as $relatedProduct) {
            $productEditPage->getUpsellBlock()->searchAndSelect(array('name' => $relatedProduct->getProductName()));
        }
        $productBlock->save($product);
        //Verify that the product was successfully saved
        $productEditPage->getMessagesBlock()->assertSuccessMessage();
    }

    /**
     * Assert configurable product is added to cart together with the proper related product
     *
     * @param SimpleProduct $simpleProduct1
     * @param SimpleProduct $simpleProduct2
     * @param ConfigurableProduct $configurableProduct
     */
    protected function assertOnTheFrontEnd($simpleProduct1, $simpleProduct2, $configurableProduct)
    {
        //Open up simple product 1 page
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->init($simpleProduct1);
        $productPage->open();
        /** @var \Magento\Catalog\Test\Block\Product\ProductList\Related $relatedProductBlock */
        $simpleProductRelatedBlock = $productPage->getRelatedProductBlock();
        $this->assertTrue($simpleProductRelatedBlock->isVisible(),
            'Related products block is not found on the simple product page');
        //Verify that simple product 2 and configurable product present as related products
        $simpleProductRelatedBlock->verifyRelatedProducts($simpleProduct2, $configurableProduct);

        //Open up configurable product page
        $configurableProductPage = Factory::getPageFactory()->getCatalogProductView();
        $configurableProductPage->init($configurableProduct);
        $configurableProductPage->open();
        /** @var \Magento\Catalog\Test\Block\Product\ProductList\Related $relatedConfigurableProductBlock */
        $configurableProductRelatedBlock = $configurableProductPage->getRelatedProductBlock();
        $this->assertTrue($configurableProductRelatedBlock->isVisible(),
            'Related products block is not found on the configurable product page');
        //Verify that simple product 2 presents as related product
        $configurableProductRelatedBlock->verifyRelatedProducts($simpleProduct2);

        //Add the configurable product and its related product together to the shopping cart
        $configurableProductRelatedBlock->addRelatedProductsToCart($configurableProductPage->getViewBlock(),
            $simpleProduct2, $configurableProduct);

        //Verify that both configurable product and simple product 2 are added to shopping cart
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartBlock = $checkoutCartPage->getCartBlock();
        $checkoutCartPage->getMessageBlock()->assertSuccessMessage();
        $this->assertTrue($checkoutCartBlock->isProductInShoppingCart($configurableProduct),
            'Configurable product was not found in the shopping cart.');
        $this->assertTrue($checkoutCartBlock->isProductInShoppingCart($simpleProduct2),
            'Related product was not found in the shopping cart.');
    }
}
