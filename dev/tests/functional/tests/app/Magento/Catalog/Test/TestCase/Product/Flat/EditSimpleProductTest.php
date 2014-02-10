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

namespace Magento\Catalog\Test\TestCase\Product\Flat;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Catalog\Test\Fixture\SimpleProduct;

/**
 * Edit product with enabled flat
 *
 * Class EditSimpleProductTest
 * @package Magento\Catalog\Test\TestCase\Product\Flat
 * @ZephyrId MAGETWO-21125
 */
class EditSimpleProductTest extends \Magento\Catalog\Test\TestCase\Product\EditSimpleProductTest
{
    protected function setUp()
    {
        parent::setUp();

        $config = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $config->switchData('enable_product_flat');
        $config->persist();
    }

    /**
     * Assert product data on category page
     *
     * @param SimpleProduct $product
     * @param string $categoryName
     */
    protected function assertOnCategoryPage($product, $categoryName)
    {
        parent::assertOnCategoryPage($product, $categoryName);
    }

    /**
     * Skip assertion on product page
     *
     * @param SimpleProduct $productOld
     * @param SimpleProduct $productEdited
     */
    protected function assertOnProductPage($productOld, $productEdited)
    {
        return;
    }

    /**
     * Disable product flat
     */
    protected function tearDown()
    {
        $config = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $config->switchData('disable_product_flat');
        $config->persist();
        parent::tearDown();
    }
}
