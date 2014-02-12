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
class Core_Mage_Vde_RouterTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/disable_secret_key');
    }

    public function tearDownAfterTestClass()
    {
        $this->admin('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/enable_secret_key');
        $this->logoutAdminUser();
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->setUrlPrefix('frontend', 'vde/');
        $this->navigate('manage_products');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-6499
     * @author roman.grebenchuk
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
        $this->assertTrue($this->vdeHelper()->isVdeRouter($this->url()));
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-6498
     * @author roman.grebenchuk
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
        $this->assertTrue($this->vdeHelper()->isVdeRouter($this->url()));
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-6500
     * @author roman.grebenchuk
     */
    public function cmsRouterTest()
    {
        $this->frontend();
        $this->clickControl('link', 'about_us');
        $this->assertTrue($this->vdeHelper()->isVdeRouter($this->url()));
    }
}
