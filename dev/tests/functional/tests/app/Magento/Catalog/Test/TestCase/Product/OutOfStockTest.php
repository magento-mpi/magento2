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

/**
 * Class OutOfStockTest
 * Test product out of stock
 */
class OutOfStockTest extends Functional
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
     * Test product out of stock adding to cart
     *
     * @ZephyrId MAGETWO-12423
     * @return void
     */
    public function testTestOutOfStockItemAddToCart()
    {
        $config = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $config->switchData('show_out_of_stock');
        $config->persist();

        $product = Factory::getFixtureFactory()->getMagentoCatalogSimpleProduct();
        $product->switchData('simple_out_of_stock');
        $product->persist();

        $productPage = Factory::getPageFactory()->getCatalogProductView();
        $productPage->init($product);
        $productPage->open();
        $this->assertFalse($productPage->getViewBlock()->checkAddToCardButton());
    }
}
