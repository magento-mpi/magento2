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
     * @param SimpleProduct $product
     * @param string $categoryName
     * @param bool $assertOnProductPage
     */
    protected function assertOnCategory($product, $categoryName, $assertOnProductPage = true)
    {
        parent::assertOnCategory($product, $categoryName, false);
    }

}
