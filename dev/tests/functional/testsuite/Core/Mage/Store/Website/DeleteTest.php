<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Store
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Delete Website into Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Store_Website_DeleteTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Manage Stores</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_stores');
    }

    /**
     * <p>Delete Website without Store</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3491
     */
    public function deleteWithoutStore()
    {
        $this->markTestIncomplete('MAGETWO-11690');
        //Preconditions
        $websiteData = $this->loadDataSet('Website', 'generic_website');
        $this->storeHelper()->createStore($websiteData, 'website');
        $this->assertMessagePresent('success', 'success_saved_website');
        //Data
        $deleteWebsiteData = array('website_name' => $websiteData['website_name']);
        //Steps
        $this->storeHelper()->deleteStore($deleteWebsiteData);
    }

    /**
     * <p>Delete Website with Store</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3492
     */
    public function deleteWithStore()
    {
        //Preconditions
        $websiteData = $this->loadDataSet('Website', 'generic_website');
        $storeData = $this->loadDataSet('Store', 'generic_store', array('website' => $websiteData['website_name']));
        $this->storeHelper()->createStore($websiteData, 'website');
        $this->assertMessagePresent('success', 'success_saved_website');
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent('success', 'success_saved_store');
        //Data
        $deleteWebsiteData = array('website_name' => $websiteData['website_name']);
        //Steps
        $this->storeHelper()->deleteStore($deleteWebsiteData);
    }

    /**
     * <p>Delete Website with Store and Store View</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3493
     */
    public function deleteWithStoreAndStoreView()
    {
        //Preconditions
        $websiteData = $this->loadDataSet('Website', 'generic_website');
        $storeData = $this->loadDataSet('Store', 'generic_store', array('website' => $websiteData['website_name']));
        $storeView =
            $this->loadDataSet('StoreView', 'generic_store_view', array('store_name' => $storeData['store_name']));
        $this->storeHelper()->createStore($websiteData, 'website');
        $this->assertMessagePresent('success', 'success_saved_website');
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent('success', 'success_saved_store');
        $this->storeHelper()->createStore($storeView, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
        //Data
        $deleteWebsiteData = array('website_name' => $websiteData['website_name']);
        //Steps
        $this->storeHelper()->deleteStore($deleteWebsiteData);
    }

    /**
     * <p>Delete Website with assigned product</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3490
     */
    public function deleteWithAssignedProduct()
    {
        //Preconditions
        $websiteData = $this->loadDataSet('Website', 'generic_website');
        $productData = $this->loadDataSet('Product', 'simple_product_visible',
            array('websites' => $websiteData['website_name']));
        $deleteWebsiteData = array('website_name' => $websiteData['website_name']);
        $this->storeHelper()->createStore($websiteData, 'website');
        $this->assertMessagePresent('success', 'success_saved_website');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/disable_single_store_mode');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->navigate('manage_stores');
        $this->storeHelper()->deleteStore($deleteWebsiteData);
    }
}