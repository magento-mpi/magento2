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

use Magento\Catalog\Test\Repository\ConfigurableProduct as Repository;
use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Apply minimum advertised price to Configurable product
 *
 * @package Magento\Catalog\Test\TestCase\Product
 */
class ApplyMapConfigurableTest extends Functional
{
    /**
     * Login into backend area before test
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
            $productListBlock->isProductVisible($product->getProductName()),
            'Product is invisible on Category page'
        );
        $this->assertFalse($productListBlock->isRegularPriceVisible(), 'Regular price is visible and not expected.');
        $this->assertContains(
            $product->getProductMapPrice(),
            $productListBlock->getOldPriceCategoryPage(),
            'Displayed on Category page MAP is incorrect'
        );
        $productListBlock->openMapBlockOnCategoryPage($product->getProductName());
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
        $productViewBlock->fillOptions($product);
        $productViewBlock->addToCart($product);
    }

    /**
     * Assert product MAP related data on cart
     *
     * @param \Magento\Catalog\Test\Fixture\ConfigurableProduct $product
     */
    protected function verifyMapInShoppingCart($product)
    {
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->getMessageBlock()->assertSuccessMessage();
        $unitPrice = $checkoutCartPage->getCartBlock()->getCartItemUnitPrice($product);
        $optionPrice = $product->getProductOptionsPrice() + floatval($product->getProductPrice());
        $this->assertEquals($optionPrice, $unitPrice, 'Incorrect price for ' . $product->getProductName());
    }

    /**
     * Disable MAP on Config level
     */
    public static function tearDownAfterClass()
    {
        $config = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $config->switchData('disable_map_config');
        $config->persist();
    }
}
