<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Store_EnableSingleStoreMode
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */

class Enterprise2_Mage_Store_EnableSingleStoreMode_SystemConfigurationTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Precondition for test:</p>
     * <p>1. Login to backend.</p>
     * <p>2. Navigate to System -> Manage Store.</p>
     * <p>3. Verify that one store-view is created.<p>
     * <p>4. Go to System - Configuration - General and enable Single-Store Mode.</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->admin('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        $config = $this->loadDataSet('SingleStoreMode', 'enable_single_store_mode');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
    }

    protected function tearDownAfterTest()
    {
        $config = $this->loadDataSet('SingleStoreMode', 'disable_single_store_mode');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
    }

    /**
     * <p>"Price" fieldset is displayed if Single Store Mode enabled.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Manage Stores</p>
     * <p>3.Verify that one store-view is created.</p>
     * <p>4.Go to System - Configuration - Catalog - Catalog</p>
     * <p>5.Expand Price fieldset and check for "Catalog Price Scope" dropdown </p>
     * <p>Expected result: </p>
     * <p>"Catalog Price Scope" dropdown is not displayed.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6182
     * @author Tatyana_Gonchar
     */
    function verificationCatalogPrice()
    {
        $this->admin('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('catalog_catalog');
        $this->assertFalse($this->controlIsPresent('dropdown', 'catalog_price_scope'),
               "Dropdown Catalog Price Scope is not present on the page");
    }
}