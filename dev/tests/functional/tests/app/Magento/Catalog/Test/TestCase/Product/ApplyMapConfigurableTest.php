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
use Magento\Catalog\Test\Repository\ConfigurableProduct as Repository;

/**
 * Apply minimum advertised price to Configurable product
 */
class ApplyMapConfigurableTest extends Functional
{
    /**
     * Login into backend area before test
     *
     * @return void
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
        // precondition 1: Configure MAP Settings. In setup() since teardown() method will disable.
        $config = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $config->switchData('enable_map_config');
        $config->persist();
    }

    /**
     * Apply minimum advertised price to configurable product
     *
     * @ZephyrId MAGETWO-12847
     * @return void
     */
    public function testApplyMapToConfigurable()
    {
        // precondition 2: Create configurable product with minimum advertised price (MAP)
        $product = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $product->switchData(Repository::CONFIGURABLE_MAP);
        $product->persist();
        //Flush cache
        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();
        $cachePage->getMessagesBlock()->assertSuccessMessage();
        //Verifying
        $this->verifyMapOnCategory($product);
        $this->verifyMapOnProductView($product);
        $this->verifyMapInShoppingCart($product);
    }

    /**
     * Assert product MAP related data on category list
     *
     * @param \Magento\Catalog\Test\Fixture\ConfigurableProduct $product
     * @return void
     */
    protected function verifyMapOnCategory($product)
    {
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $frontendHomePage->open();
        $frontendHomePage->getTopmenu()->selectCategoryByName($product->getCategoryName());
        // Verify on category product list
        $productListBlock = $categoryPage->getListProductBlock();
        $this->assertTrue(
            $productListBlock->isProductVisible($product->getName()),
            'Product is invisible on Category page'
        );
        $this->assertFalse($productListBlock->isRegularPriceVisible(), 'Regular price is visible and not expected.');
        $this->assertContains(
            $product->getProductMapPrice(),
            $productListBlock->getOldPriceCategoryPage(),
            'Displayed on Category page MAP is incorrect'
        );
        $productListBlock->openMapBlockOnCategoryPage($product->getName());
        $mapBlock = $categoryPage->getMapBlock();
        $this->assertContains(
            $product->getProductMapPrice(),
            $mapBlock->getOldPrice(),
            'Displayed on Category page MAP is incorrect'
        );
        $this->assertEquals(
            $product->getProductPrice(),
            $mapBlock->getActualPrice(),
            'Displayed on Category page price is incorrect'
        );
        $mapBlock->addToCartFromMap();
    }

    /**
     * Assert product MAP related data on product view
     *
     * @param \Magento\Catalog\Test\Fixture\ConfigurableProduct $product
     * @return void
     */
    protected function verifyMapOnProductView($product)
    {
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->getMessagesBlock()->assertNoticeMessage();
        $productViewBlock = $productPage->getViewBlock();
        $productPriceBlock = $productViewBlock->getProductPriceBlock();
        $this->assertFalse($productPriceBlock->isRegularPriceVisible(), 'Regular price is visible and not expected.');
        $productViewBlock->openMapBlockOnProductPage();
        $mapBlock = $productPage->getMapBlock();
        $this->assertContains(
            $product->getProductMapPrice(),
            $mapBlock->getOldPrice(),
            'Displayed on Product page MAP is incorrect'
        );
        $this->assertEquals(
            $product->getProductPrice(),
            $mapBlock->getActualPrice(),
            'Displayed on Product page price is incorrect'
        );
        $mapBlock->closeMapBlock();

        $optionsBlock = $productPage->getCustomOptionsBlock();
        $productOptions = $product->getProductOptions();
        if (!empty($productOptions)) {
            $optionsBlock->fillProductOptions($productOptions);
        }
        $productViewBlock->clickAddToCart();
    }

    /**
     * Assert product MAP related data on cart
     *
     * @param \Magento\Catalog\Test\Fixture\ConfigurableProduct $product
     * @return void
     */
    protected function verifyMapInShoppingCart($product)
    {
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->getMessagesBlock()->assertSuccessMessage();
        $unitPrice = $checkoutCartPage->getCartBlock()->getCartItemUnitPrice($product);
        $optionPrice = $product->getProductOptionsPrice() + floatval($product->getProductPrice());
        $this->assertEquals($optionPrice, $unitPrice, 'Incorrect price for ' . $product->getName());
    }

    /**
     * Disable MAP on Config level
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        $config = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $config->switchData('disable_map_config');
        $config->persist();
    }
}
