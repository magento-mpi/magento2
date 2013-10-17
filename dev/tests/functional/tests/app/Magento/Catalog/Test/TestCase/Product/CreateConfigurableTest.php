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

use Magento\Catalog\Test\Fixture\ConfigurableProduct;
use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Mtf\Util\FixtureIterator;

/**
 * Class CreateTest
 * Test product creation
 *
 * @package Magento\Catalog\Test\TestCase\Product
 */
class CreateConfigurableTest extends Functional
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
     * @param ConfigurableProduct $product
     * @dataProvider dataProviderTestCreateProduct
     */
    public function testCreateConfigurableProduct(ConfigurableProduct $product)
    {
        //Data
        $createProductPage = Factory::getPageFactory()->getAdminCatalogProductNew();
        $manageProductsGrid = Factory::getPageFactory()->getAdminCatalogProductIndex();
        $productBlockForm = $createProductPage->getProductBlockForm();
        $variations = $product->getVariations();
        //Steps
        $manageProductsGrid->open();
        $manageProductsGrid->getProductBlock()->addProduct('configurable');

        $productBlockForm->fill($product);
        $variationsBlock = $createProductPage->getVariationsBlock();
        $variationsBlock->selectAttribute('dd');

        $variationsForm = $createProductPage->getVariationsForm();

        $variationsForm->fillFormPrice($variations);
        $variationsBlock->generateVariations();

        $currentVariations = $createProductPage->getCurrentVariations();
        $currentVariations->fillFormQty($variations);

        $productBlockForm->save($product);
        $affectedAttributeSetChooser = $createProductPage->getAffectedAttributeSetChooser();
        $affectedAttributeSetChooser->chooseNewAndConfirm('attr_set1');
        //Verifying
        $createProductPage->assertProductSaveResult($product);
    }

    /**
     * @return ConfigurableProduct
     */
    public function dataProviderTestCreateProduct()
    {
        return new FixtureIterator(Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct());
    }
}
