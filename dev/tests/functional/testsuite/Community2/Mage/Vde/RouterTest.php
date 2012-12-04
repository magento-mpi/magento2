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
/**
 * @method Core_Mage_Vde_Helper vdeHelper() vdeHelper()
 */
class Community2_Mage_Vde_RouterTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->setUrlPrefix('frontend', 'vde/');
        $this->navigate('manage_products');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-6499
     */
    public function rewriteRouterTest()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->reindexInvalidedData();
        $this->clearInvalidedCache();
        $this->productHelper()->frontOpenProduct($productData['general_name']);
        $this->assertTrue($this->vdeHelper()->isVdeRouter($this->getLocation()));
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-6498
     */
    public function defaultRouterTest()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //get product Id
        $this->productHelper()->openProduct(array('name' => $productData['general_name']));
        $productId = $this->defineIdFromUrl();
        $this->reindexInvalidedData();
        $this->clearInvalidedCache();
        $this->productHelper()->frontOpenProductById($productId, $productData['general_name']);
        $this->assertTrue($this->vdeHelper()->isVdeRouter($this->getLocation()));
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-6500
     */
    public function cmsRouterTest()
    {
        $this->frontend('about_us');
        $this->assertTrue($this->vdeHelper()->isVdeRouter($this->getLocation()));
    }
}