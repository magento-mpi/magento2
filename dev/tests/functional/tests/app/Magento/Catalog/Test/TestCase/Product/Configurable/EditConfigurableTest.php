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

namespace Magento\Catalog\Test\TestCase\Product\Configurable;

use Magento\Catalog\Test\TestCase\Product\CreateConfigurableTest;
use Mtf\Factory\Factory;

/**
 * Class EditConfigurableTest
 * Edit Configurable product
 *
 * @package Magento\Catalog\Test\TestCase\Product\Configurable
 */
class EditConfigurableTest extends CreateConfigurableTest
{
    /**
     * Edit configurable product and add new options to attribute
     *
     * @ZephyrId MAGETWO-12840
     */
    public function testCreateConfigurableProduct()
    {
        //Preconditions
        //Preparing Data for original product
        $configurable = Factory::getFixtureFactory()->getMagentoCatalogConfigurableProduct();
        $configurable->switchData('configurable');
        $configurable->persist();
        $productSku = $configurable->getProductSku();
        //Preparing Data for editing product
        $editProduct = $configurable->getEditData();
        //Steps
        $createProductPage = Factory::getPageFactory()->getCatalogProductNew();
        $productBlockForm = $createProductPage->getProductBlockForm();
        //Login
        Factory::getApp()->magentoBackendLoginUser();
        $productGridPage = Factory::getPageFactory()->getCatalogProductIndex();
        $productGridPage->open();
        //Search and open original configurable product
        $productGridPage->getProductGrid()->searchAndOpen(array('sku' => $productSku));
        //Editing product options
        $productBlockForm->fill($editProduct);
        $productBlockForm->save($editProduct);
        //Verifying
        $createProductPage->getMessagesBlock()->assertSuccessMessage();
        //Flush cache
        $cachePage = Factory::getPageFactory()->getAdminCache();
        $cachePage->open();
        $cachePage->getActionsBlock()->flushMagentoCache();
        //Verifying
        $this->assertOnGrid($editProduct);
        $this->assertOnFrontend($editProduct);
    }
}
