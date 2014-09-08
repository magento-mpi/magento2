<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product\Configurable;

use Mtf\Factory\Factory;
use Magento\Catalog\Test\TestCase\Product\CreateConfigurableTest;

/**
 * Class EditConfigurableTest
 * Edit Configurable product
 */
class EditConfigurableTest extends CreateConfigurableTest
{
    /**
     * Edit configurable product and add new options to attribute
     *
     * @ZephyrId MAGETWO-12840
     * @return void
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
        $productForm = $createProductPage->getProductForm();
        //Login
        Factory::getApp()->magentoBackendLoginUser();
        $productGridPage = Factory::getPageFactory()->getCatalogProductIndex();
        $productGridPage->open();
        //Search and open original configurable product
        $productGridPage->getProductGrid()->searchAndOpen(['sku' => $productSku]);
        //Editing product options
        $productForm->fill($editProduct);
        $createProductPage->getFormAction()->save();
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
