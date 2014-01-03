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
    }

    /**
     * Apply minimum advertised price to configurable product
     *
     * @ZephyrId MAGETWO-12847
     */
    public function testApplyMapToConfigurable()
    {
        // precondition 1: Configure MAP Settings
        $config = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $config->switchData('enable_map_config');
        $config->persist();
        // precondition 2: Add configurable product with minimum advertised price (MAP)
        $configurable = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $configurable->switchData(Repository::CONFIGURABLE_MAP);
        $configurable->persist();
        //Flush cache
        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();
        $cachePage->getMessagesBlock()->assertSuccessMessage();
        //Verifying
        $this->verifyMap($configurable);
    }

    /**
     * Assert product MAP related data on storefront
     *
     * @param \Magento\Catalog\Test\Fixture\ConfigurableProduct $product
     */
    protected function verifyMap($product)
    {
        $frontendHomePage = Factory::getPageFactory()->getCmsIndexIndex();
        $categoryPage = Factory::getPageFactory()->getCatalogCategoryView();
        $frontendHomePage->open();
        $frontendHomePage->getTopmenu()->selectCategoryByName($product->getCategoryName());
        // Verify on category product list
        $productListBlock = $categoryPage->getListProductBlock();
        $mapBlock = $categoryPage->getMapBlock();
        $this->assertTrue(
            $productListBlock->isProductVisible($product->getProductName()),
            'Product is invisible on Category page'
        );
        $this->assertContains(
            $product->getProductMapPrice(),
            $productListBlock->getOldPriceCategoryPage(),
            'Displayed on Category page MAP is incorrect'
        );
        $productListBlock->openMapBlockOnCategoryPage($product->getProductName());
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
        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->getMessagesBlock()->assertNoticeMessage();
        $productViewBlock = $productPage->getViewBlock();
        $productViewBlock->openMapBlockOnProductPage();
        $mapBlock = $productPage->getMapBlock();
        // Verify on Product View page
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
        // Verify Cart page price
        $productViewBlock->fillOptions($product);
        $productViewBlock->addToCart($product);
        $checkoutCartPage = Factory::getPageFactory()->getCheckoutCart();
        $checkoutCartPage->getMessageBlock()->assertSuccessMessage();
        $unitPrice = $checkoutCartPage->getCartBlock()->getCartItemUnitPrice($product);
        $optionPrice = $product->getProductOptionsPrice() + floatval($product->getProductPrice());
        $this->assertEquals($optionPrice, $unitPrice, 'Incorrect price for ' . $product->getProductName());
    }
}
