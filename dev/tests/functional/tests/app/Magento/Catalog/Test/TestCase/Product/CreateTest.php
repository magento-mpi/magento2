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
use Mtf\Util\FixtureIterator;
use Magento\Catalog\Test\Fixture\Product;

/**
 * Class CreateTest
 * Test product creation
 *
 * @package Magento\Catalog\Test\TestCase\Product
 */
class CreateTest extends Functional
{
    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Test product create
     *
     * @param Product $product
     * @dataProvider dataProviderTestCreateProduct
     */
    public function testCreateProduct(Product $product)
    {
        //Data
        $createProductPage = Factory::getPageFactory()->getAdminCatalogProductNew();
        $createProductPage->init($product);
        $productBlockForm = $createProductPage->getProductBlockForm();
        //Steps
        $createProductPage->open();
        $productBlockForm->fill($product);
        $productBlockForm->save($product);
        //Verifying
        $createProductPage->assertProductSaveResult($product);
    }

    /**
     * @return Product
     */
    public function dataProviderTestCreateProduct()
    {
        return new FixtureIterator(Factory::getFixtureFactory()->getMagentoCatalogProduct());
    }
}
