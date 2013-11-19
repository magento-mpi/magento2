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
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Catalog\Test\Fixture\Product;
use Magento\Catalog\Test\Page\Product\CatalogProductEdit;

/**
 * Class PromoteRelatedProductTest
 * Test promoting products as related
 *
 * @package Magento\Catalog\Test\TestCase\Product
 */
class PromoteRelatedProductTest extends Functional
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
    public function testPromoteRelatedProduct()
    {

        // Setup preconditions for MAGETWO-12392

        // Precondition: create simple product 1
        $simpleProduct1 = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $simpleProduct1->persist();
        // Precondition: create simple product 2
        $simpleProduct2 = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $simpleProduct2->persist();
        // Precondition: create configurable product
        $configurableProduct = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $configurableProduct->persist();

        $this->addRelatedProduct($simpleProduct1, array($simpleProduct2, $configurableProduct));
        $this->addRelatedProduct($configurableProduct, array($simpleProduct2));
        $this->assertOnTheFrontend($simpleProduct1, $simpleProduct2, $configurableProduct);
    }

    /**
     * Configure related products in the backend
     *
     * @param Product $product
     * @array Product $relatedProducts
     * @param $relatedProducts
     */
    private function addRelatedProduct($product, $relatedProducts)
    {
        foreach ($relatedProducts as $relatedProduct) {
            $productEditPage = Factory::getPageFactory()->getCatalogProductEdit();
            $productEditPage->open(array('id' => $product->getProductId()));
            $this->directToRelatedProductPage($productEditPage);
            $productEditPage->getProductBlockForm()
                ->waitForElementVisible('[title="Reset Filter"][class*=action]', Locator::SELECTOR_CSS);
            $productEditPage->getRelatedProductEditGrid()
                ->searchAndSelect(array('name' => $relatedProduct->getProductName()));
            $productEditPage->getProductBlockForm()->save($product);
            //Verify that the product was successfully saved
            $this->assertSuccessMessage("You saved the product.", $productEditPage);
        }
    }

    /**
     * @param CatalogProductEdit $productEditPage
     */
    private function directToRelatedProductPage($productEditPage)
    {
        $productBlockForm = $productEditPage->getProductBlockForm();
        /**
         * Open tab "Advanced Settings" to make all nested tabs visible and available to interact
         */
        $productBlockForm->getRootElement()
            ->find('ui-accordion-product_info_tabs-advanced-header-0', Locator::SELECTOR_ID)->click();

        /**
         * Wait for the "related tab" shows up and click on it
         */
        $productBlockForm->waitForElementVisible('product_info_tabs_related', Locator::SELECTOR_ID);
        $productBlockForm->getRootElement()
            ->find('product_info_tabs_related', Locator::SELECTOR_ID)->click();
    }

    /**
     * @param $messageText
     * @param CatalogProductEdit $productEditPage
     */
    private function assertSuccessMessage($messageText, $productEditPage)
    {
        $messageBlock = $productEditPage->getMessagesBlock();
        $this->assertContains(
            $messageText,
            $messageBlock->getSuccessMessages(),
            sprintf('Message "%s" is not appear.', $messageText)
        );
    }

    /**
     * Assert configurable product is added to cart together with the proper related product
     *
     * @param Product $simpleProduct1
     * @param Product $simpleProduct2
     * @param Product $configurableProduct
     */
    protected function assertOnTheFrontEnd($simpleProduct1, $simpleProduct2, $configurableProduct)
    {
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->init($simpleProduct1);
        $productPage->open();
        $this->verifySimpleProduct($productPage->getViewBlock(), $simpleProduct2, $configurableProduct);
        $this->verifyConfigurableProduct($simpleProduct2, $configurableProduct);
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartBlock = $checkoutCartPage->getCartBlock();
        $checkoutCartPage->getMessageBlock()->assertSuccessMessage();
        $this->assertTrue($checkoutCartBlock->checkAddedProduct($configurableProduct)->isVisible(),
            'Configurable product was not found in the shopping cart.');
        $this->assertTrue($checkoutCartBlock->checkAddedProduct($simpleProduct2)->isVisible(),
            'Related product was not found in the shopping cart.');
    }

    /**
     * Verify that the simple product 1 has simple product 2 and configurable product as related products
     *
     * @param \Magento\Catalog\Test\Block\Product\View $productViewBlock
     * @param Product $simpleProduct2
     * @param Product $configurableProduct
     */
    private function verifySimpleProduct($productViewBlock, $simpleProduct2, $configurableProduct)
    {
        $rootElement = $productViewBlock->getRootElement();

        //Verify that related products section(block) is present on the product page
        $this->assertTrue($rootElement->find('[class="block related"]')->isVisible(),
            'Related products block is not found on the simple product page');

        //Verify that simple product 2 is added as related product and has checkbox
        $this->assertTrue($rootElement->find('[title="'. $simpleProduct2->getProductName() . '"]')->isVisible(),
            'Simple product 2 is not added successfully as related product');
        $this->assertTrue($rootElement
                ->find('related-checkbox' . $simpleProduct2->getProductId(), Locator::SELECTOR_ID)->isVisible(),
            'Simple product 2 does not have "Add to Cart" checkbox');

        //Verify that configurable product is added as related product and does not have checkbox
        $this->assertTrue($productViewBlock->getRootElement()
                ->find('[title="'. $configurableProduct->getProductName() . '"]')->isVisible(),
            'Configurable product is not added successfully as related product');
        $this->assertFalse($rootElement
                ->find('related-checkbox' . $configurableProduct->getProductId(), Locator::SELECTOR_ID)->isVisible(),
            'Configurable product should not have "Add to Cart" checkbox');
    }

    /**
     * Verify that the configurable product has simple product 2 as related product
     *
     * @param Product $simpleProduct2
     * @param Product $configurableProduct
     */
    private function verifyConfigurableProduct($simpleProduct2, $configurableProduct)
    {
        //Open up configurable product page
        $configurableProductPage = Factory::getPageFactory()->getCatalogProductView();
        $configurableProductPage->init($configurableProduct);
        $configurableProductPage->open();
        $rootElement = $configurableProductPage->getViewBlock()->getRootElement();

        //Verify that related products section(block) is present on the product page
        $this->assertTrue($rootElement->find('[class="block related"]')->isVisible(),
            'Related products block is not found on the configurable product page');

        //Verify that simple product 2 is added as related product and has checkbox
        $this->assertTrue($rootElement->find('[title="'. $simpleProduct2->getProductName() . '"]')->isVisible(),
            'Simple product 2 is not added successfully as related product');
        $this->assertTrue($rootElement
                ->find('related-checkbox' . $simpleProduct2->getProductId(), Locator::SELECTOR_ID)->isVisible(),
            'Simple product 2 does not have "Add to Cart" checkbox');

        //Add configurable and the related product for configurable together to the shopping cart
        $configurableProductPage->getViewBlock()->addRelatedProductsToCart($simpleProduct2, $configurableProduct);
    }
}
