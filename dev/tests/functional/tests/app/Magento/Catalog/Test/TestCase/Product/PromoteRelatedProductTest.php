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
use Magento\Backend\Test\Block\Widget\FormTabs;
use Magento\Catalog\Test\Block\Product\Configurable\AffectedAttributeSet;
use Magento\Catalog\Test\Fixture\Product;

/**
 * Create simple product for BAT
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
    public function testCreateProductAdvancedInventory()
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

        // Steps
        //$productEditPage = Factory::getPageFactory()->getCatalogProductEdit();
        //$productEditPage->open(array('id' => $simpleProduct1->getProductId()));
        //$this->directToRelatedProductPage($productEditPage);

        $this->addRelatedProduct($simpleProduct1, array($simpleProduct2, $configurableProduct));
        $this->addRelatedProduct($configurableProduct, array($simpleProduct2));
        $this->assertOnProductPages($simpleProduct1, $simpleProduct2, $configurableProduct);
    }

    /**
     * @param \Magento\Catalog\Test\Page\Product\CatalogProductEdit $productEditPage
     */
    protected function directToRelatedProductPage($productEditPage)
    {
        $productBlockForm = $productEditPage->getProductBlockForm();
        /**
         * Open tab "Advanced Settings" to make all nested tabs visible and available to interact
         */
        $productBlockForm->getRootElement()
            ->find('ui-accordion-product_info_tabs-advanced-header-0', Locator::SELECTOR_ID)->click();

        /**
         * comments here
         */
        $productBlockForm->waitForElementVisible('product_info_tabs_related', Locator::SELECTOR_ID);
        $productBlockForm->getRootElement()
            ->find('product_info_tabs_related', Locator::SELECTOR_ID)->click();
    }

    /**
     * @param \Magento\Catalog\Test\Fixture\Product $product
     * @array \Magento\Catalog\Test\Fixture\Product $relatedProducts
     * @param $relatedProducts
     */
    protected function addRelatedProduct($product, $relatedProducts)
    {
        foreach ($relatedProducts as $relatedProduct) {
            $productEditPage = Factory::getPageFactory()->getCatalogProductEdit();
            $productEditPage->open(array('id' => $product->getProductId()));
            $this->directToRelatedProductPage($productEditPage);
            $productEditPage->getProductBlockForm()
                ->waitForElementVisible('[title="Reset Filter"][class*=action]', Locator::SELECTOR_CSS);
            $productEditPage->getProductEditGrid()->searchAndSelect(array('name' => $relatedProduct->getProductName()));
            $productEditPage->getProductBlockForm()->save($product);
            //Verify that the product was successfully saved
            $this->assertSuccessMessage("You saved the product.", $productEditPage);
        }
    }

    /**
     * @param $messageText
     * @param \Magento\Catalog\Test\Page\Product\CatalogProductEdit $productEditPage
     */
    protected function assertSuccessMessage($messageText, $productEditPage)
    {
        $messageBlock = $productEditPage->getMessagesBlock();
        $this->assertContains(
            $messageText,
            $messageBlock->getSuccessMessages(),
            sprintf('Message "%s" is not appear.', $messageText)
        );
    }

    /**
     * Assert input product has proper related products in the front end
     *
     * @param \Magento\Catalog\Test\Fixture\Product $simpleProduct1
     * @param \Magento\Catalog\Test\Fixture\Product $simpleProduct2
     * @param \Magento\Catalog\Test\Fixture\Product $configurableProduct
     */
    protected function assertOnProductPages($simpleProduct1, $simpleProduct2, $configurableProduct)
    {
        //Pages
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $resultPage = Factory::getPageFactory()->getCatalogsearchResult();
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();

        //Blocks
        $productViewBlock = $productPage->getViewBlock();
        $productListBlock = $resultPage->getListProductBlock();
        $checkoutCartBlock = $checkoutCartPage->getCartBlock();

        //Steps
        $frontendHomePage->open();
        $frontendHomePage->getSearchBlock()->search($simpleProduct1->getProductSku());

        //Verifying
        $this->assertTrue($productListBlock->isProductVisible($simpleProduct1->getProductName()),
            'Product was not found.');
        $productListBlock->openProductViewPage($simpleProduct1->getProductName());
        $this->assertEquals($simpleProduct1->getProductName(), $productViewBlock->getProductName(),
            'Wrong product page has been opened.');

        //Click on configurable product in related products section
        $productViewBlock->getRelatedProductsOption($simpleProduct1, $simpleProduct2, $configurableProduct);

        //Add configurable product to shopping cart
        $productViewBlock->addToCart($configurableProduct);
        $checkoutCartPage->getMessageBlock()->assertSuccessMessage();
        $flag = $checkoutCartBlock->checkAddedProduct($simpleProduct2)->isVisible();
        $this->assertTrue($flag, 'Related product was not found in the shopping cart.');
    }
}
