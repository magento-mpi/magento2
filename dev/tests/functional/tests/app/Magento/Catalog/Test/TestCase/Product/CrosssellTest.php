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
use Magento\Catalog\Test\Fixture\Product;

class CrosssellTest extends Functional
{
    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Product Cross-selling. Assign cross-selling to products and see them related on the front-end.
     *
     * @ZephyrId MAGETWO-12390
     */
    public function testCreateCrosssell()
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

        $this->addCrosssellProducts($simple1, array($simple2, $configurable));
        $this->addCrosssellProducts($configurable, array($simple1, $simple2));

        //Ensure shopping cart is empty
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->open();
        $checkoutCartPage->getCartBlock()->clearShoppingCart();

        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->init($simple1);
        $productPage->open();
        $productPage->getViewBlock()->addToCart($simple1);

        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->getMessageBlock()->assertSuccessMessage();

        $cartBlock = $checkoutCartPage->getCartBlock();
        $this->assertTrue($cartBlock->isProductInShoppingCart($simple1));

        $crosssellBlock = $checkoutCartPage->getCrosssellBlock();

        $this->assertTrue($crosssellBlock->isVisible(), "cross-sell view not found");

        $this->assertTrue(
            $crosssellBlock->verifyProductcrosssell($configurable),
            'Cross-sell product ' . $configurable->getProductName() . ' was not found in the first product page.'
        );

        $this->assertTrue(
            $crosssellBlock->verifyProductcrosssell($simple2),
            'Upsell product ' . $simple2->getProductName() . ' was not found in the first product page.'
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
     */
    private function addCrosssellProducts($product, $crosssellProducts)
    {
        $crosssellFixture = Factory::getFixtureFactory()->getMagentoCatalogCrosssellProducts();
        $crosssellFixture->setProducts($crosssellProducts);
        $crosssellFixture->switchData('add_crosssell_products');
        //Data
        $productGridPage = Factory::getPageFactory()->getCatalogProductIndex();
        $editProductPage = Factory::getPageFactory()->getCatalogProductEdit();
        //Steps
        $productGridPage->open();
        $productGridPage->getProductGrid()->searchAndOpen(array('sku' => $product->getProductSku()));
        $editProductPage->getProductBlockForm()->fill($crosssellFixture);
        $editProductPage->getProductBlockForm()->save($crosssellFixture);
        $editProductPage->getMessagesBlock()->assertSuccessMessage();
    }
}
