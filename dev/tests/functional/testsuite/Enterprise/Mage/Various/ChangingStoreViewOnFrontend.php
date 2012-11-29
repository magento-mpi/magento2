<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Various
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */
class Enterprise_Mage_Various_ChangingStoreViewOnFrontend extends Mage_Selenium_TestCase
{
    /**
     * <p>Bug Cover<p/>
     * <p>Verification of MAGETWO-3620:</p>
     * <p>Store switcher does not work on the frontend(enterprise theme)</p>
     * <p>Steps</p>
     * <p>1. Login to backend</p>
     * <p>2. Create Store and Store Views</p>
     * <p>3. Make reindex, flush cache</p>
     * <p>4. Go to frontend and change store</p>
     * <p>5. Try to change store view on new store</p>
     * <p>Expected Result:</p>
     * <p>Store view should be changed without errors</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6443
     */
    public function changingStoreViewOnEnterpriseTheme()
    {
        $storeData = $this->loadDataSet('Store', 'generic_store');
        $firstStoreViewData =
            $this->loadDataSet('StoreView', 'generic_store_view', array('store_name' => $storeData['store_name']));
        $secondStoreViewData =
            $this->loadDataSet('StoreView', 'generic_store_view', array('store_name' => $storeData['store_name']));
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('general_design');
        $xpath = $this->_getControlXpath('link', 'design_theme_link');
        if (!$this->elementIsPresent($xpath . "[@class='open']")) {
            $this->clickControl('link', 'design_theme_link', false);
        }
        $dropdownXpath = $this->_getControlXpath('dropdown', 'design_theme');
        if (!$this->elementIsPresent(
            $dropdownXpath . "//option[@selected='selected' and @value='enterprise/default/default']")
        ) {
            $this->fillDropdown('design_theme', 'enterprise/default/default');
            $this->clickButton('save_config');
            $this->assertMessagePresent('success', 'success_saved_config');
        }
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent('success', 'success_saved_store');
        $this->storeHelper()->createStore($firstStoreViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
        $this->storeHelper()->createStore($secondStoreViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
        $this->reindexInvalidedData();
        $this->flushCache();
        $this->frontend();
        $this->addParameter('store', $storeData['store_name']);
        $this->addParameter('storeViewCode', $secondStoreViewData['store_view_code']);
        $this->clickControl('link', 'select_store_link');
        $this->selectFrontStoreView($secondStoreViewData['store_view_name']);
        $this->validatePage();
    }
}