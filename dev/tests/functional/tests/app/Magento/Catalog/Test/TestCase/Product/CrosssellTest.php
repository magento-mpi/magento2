<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product;

use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Catalog\Test\Fixture\Product;

/**
 * Class CrosssellTest
 * Test cross sell product
 */
class CrosssellTest extends Functional
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
     * Product Cross-selling. Assign cross-selling to products and see them related on the front-end.
     *
     * @ZephyrId MAGETWO-12390
     * @return void
     */
    public function testCreateCrosssell()
    {
        $simple1 = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $simple1->switchData('simple');
        $simple1->persist();

        $simple2 = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $simple2->switchData('simple');
        $simple2->persist();

        $configurable = Factory::getFixtureFactory()->getMagentoConfigurableProductConfigurableProduct();
        $configurable->switchData('configurable');
        $configurable->persist();

        $this->addCrosssellProducts($simple1, [$simple2, $configurable]);
        $this->addCrosssellProducts($configurable, [$simple1, $simple2]);

        //Ensure shopping cart is empty
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();

        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->init($simple1);
        $productPage->open();
        $productPage->getViewBlock()->addToCart($simple1);

        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->getMessagesBlock()->assertSuccessMessage();

        $cartBlock = $checkoutCartPage->getCartBlock();
        $this->assertTrue($cartBlock->isProductInShoppingCart($simple1));

        $crosssellBlock = $checkoutCartPage->getCrosssellBlock();

        $this->assertTrue($crosssellBlock->isVisible(), "cross-sell view not found");

        $this->assertTrue(
            $crosssellBlock->verifyProductcrosssell($configurable),
            'Cross-sell product ' . $configurable->getName() . ' was not found in the first product page.'
        );

        $this->assertTrue(
            $crosssellBlock->verifyProductcrosssell($simple2),
            'Upsell product ' . $simple2->getName() . ' was not found in the first product page.'
        );

        $crosssellBlock = $checkoutCartPage->getCrosssellBlock();
        $crosssellBlock->clickLink($configurable);

        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->init($configurable);
        $productPage->open();
        $productPage->getViewBlock()->addToCart($configurable);

        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $cartBlock = $checkoutCartPage->getCartBlock();
        $this->assertTrue($cartBlock->isProductInShoppingCart($configurable));
        $this->assertTrue($cartBlock->isProductInShoppingCart($simple1));

        $crosssellBlock = $checkoutCartPage->getCrosssellBlock();
        $crosssellBlock->clickLink($simple2);

        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->init($simple2);
        $productPage->open();
        $productPage->getViewBlock()->addToCart($simple2);

        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $cartBlock = $checkoutCartPage->getCartBlock();
        $this->assertTrue($cartBlock->isProductInShoppingCart($configurable));
        $this->assertTrue($cartBlock->isProductInShoppingCart($simple1));
        $this->assertTrue($cartBlock->isProductInShoppingCart($simple2));

        $this->assertFalse($checkoutCartPage->getCrosssellBlock()->isVisible());
    }

    /**
     * Assign an array of products as cross-sells to the passed in $product
     *
     * @param Product $product
     * @param array $crosssellProducts
     * @return void
     */
    private function addCrosssellProducts(Product $product, array $crosssellProducts)
    {
        $crosssellFixture = Factory::getFixtureFactory()->getMagentoCatalogCrosssellProducts();
        $crosssellFixture->setProducts($crosssellProducts);
        $crosssellFixture->switchData('add_crosssell_products');
        //Data
        $productGridPage = Factory::getPageFactory()->getCatalogProductIndex();
        $editProductPage = Factory::getPageFactory()->getCatalogProductEdit();
        //Steps
        $productGridPage->open();
        $productGridPage->getProductGrid()->searchAndOpen(['sku' => $product->getProductSku()]);
        $editProductPage->getProductForm()->fill($crosssellFixture);
        $editProductPage->getFormPageActions()->save();
        $editProductPage->getMessagesBlock()->assertSuccessMessage();
    }
}
