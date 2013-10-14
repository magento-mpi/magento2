<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Class BundleTest
 * Bundle product creation tests
 *
 * @package Magento\Bundle\Test\TestCase
 */
class BundleTest extends Functional
{
    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Create bundle
     */
    public function testCreate()
    {
        //Data
        $bundle = Factory::getFixtureFactory()->getMagentoBundleBundle()->switchData('bundle_fixed');
        //Pages & Blocks
        $manageProductsGrid = Factory::getPageFactory()->getAdminCatalogProductIndex();
        $createProductPage = Factory::getPageFactory()->getAdminCatalogProductNew();
        $productBlockForm = $createProductPage->getProductBlockForm();
        //Steps
        $manageProductsGrid->open();
        $manageProductsGrid->getProductBlock()->addProduct('bundle');
        $productBlockForm->fill($bundle);
        $productBlockForm->save($bundle);
        //Verification
        $createProductPage->assertProductSaveResult($bundle);
    }
}
