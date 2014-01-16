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

/**
 * Class OutOfStockTest
 *
 * Test product out of stock
 *
 * @package Magento\Catalog\Test\TestCase\Product
 */
class OutOfStockTest extends Functional
{
    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Test product out of stock adding to cart
     *
     * @ZephyrId MAGETWO-12423
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
        $this->assertFalse($productPage->getViewBlock()->isAddToCartButtonVisible());
    }
}
