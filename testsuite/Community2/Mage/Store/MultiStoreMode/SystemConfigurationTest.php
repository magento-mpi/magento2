<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Store
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */

class Community2_Mage_Store_MultiStoreMode_SystemConfigurationTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Precondition for test:</p>
     * <p>1. Login to backend.</p>
     * <p>2. Navigate to System -> Manage Store.</p>
     * <p>3.If there only one Store View - create one more (for enabling Multi Store Mode functionality).</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->admin('manage_stores');
        $tableXpath = $this->_getControlXpath('pageelement', 'stores_table');
        $titleRowCount = $this->getXpathCount($tableXpath . '//tr[@title]');
        $columnId = $this->getColumnIdByName('Store View Name') - 1;
        $storeViews = array();
        for ($rowId = 0; $rowId < $titleRowCount; $rowId++) {
            $storeView = $this->getTable($tableXpath . '.' . $rowId . '.' . $columnId);
            if (!in_array($storeView, array('Default Store View'))) {
                $storeViews[] = $storeView;
            }
        }
        $isEmpty = array_filter($storeViews);
        if (empty($isEmpty)){
            $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view');
            $this->storeHelper()->createStore($storeViewData, 'store_view');
        }
    }

    protected function tearDownAfterTest()
    {
        $config = $this->loadDataSet('SingleStoreMode', 'disable_single_store_mode');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
    }

    /**
     * <p>Scope Selector is enabled.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Configuration - General.</p>
     * <p>3.Expand Single Store Mode fieldset and enable Single Store Mode </p>
     * <p>4.Check for Scope selector</p>
     * <p>Expected result: </p>
     * <p>Scope Selector is displayed.</p>
     * <p>5.Repeat previous step with disabled Single-Store Mode.</p>
     * <p>Expected result: </p>
     * <p>Scope Selector is displayed.</p>
     *
     * @dataProvider singleStoreModeEnablerDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6186
     * @author Tatyana_Gonchar
     */
    function verificationScopeSelector($singleStoreModeEnabler)
    {
        $config = $this->loadDataSet('SingleStoreMode', $singleStoreModeEnabler);
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->assertTrue($this->controlIsPresent('fieldset', 'current_configuration_scope'),
               'There is no Scope Selector');
    }

    public function singleStoreModeEnablerDataProvider()
    {
        return array(
            array('enable_single_store_mode'),
            array('disable_single_store_mode')
        );
    }

    public function diffScopeSingleStoreDataProvider()
    {
        return array(
            array('Main Website', 'enable_single_store_mode'),
            array('Main Website', 'disable_single_store_mode'),
            array('Default Store View', 'enable_single_store_mode'),
            array('Default Store View', 'disable_single_store_mode'),
            array('Default Config', 'enable_single_store_mode'),
            array('Default Config', 'disable_single_store_mode')
        );
    }

    /**
     * <p>"Export Table Rates" functionality is enabled only on Website scope.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Configuration - General, expand Single-Store Mode fieldset and enable Single Store Mode</p>
     * <p>3.Select "Main Website" on the scope switcher</p>
     * <p>4.Go to Sales - Shipping Methods.</p>
     * <p>5.Check for "Table Rates" fieldset.</p>
     * <p>Expected result: </p>
     * <p>"Export CSV" button is displayed.</p>
     * <p>6.Change the scope to "Default Store View" or "Default Config".</p>
     * <p>Expected result: </p>
     * <p>"Export CSV" button is not displayed.</p>
     * <p>7.Repeat previous steps with disabled Single-Store Mode.</p>
     * <p>Expected result:</p>
     * <p>The same results as in previous case.</p>
     *
     * @dataProvider diffScopeSingleStoreDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6187
     * @author Tatyana_Gonchar
     */
    function verificationTableRatesExport($diffScope, $singleStoreModeEnabler)
    {
        $config = $this->loadDataSet('SingleStoreMode', $singleStoreModeEnabler);
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->systemConfigurationHelper()->changeConfigurationScope('current_configuration_scope', $diffScope);
        $this->systemConfigurationHelper()->openConfigurationTab('sales_shipping_methods');
        $button = 'table_rates_export_csv';
        if ($diffScope == 'Main Website') {
            if (!$this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is not present on the page on $diffScope");
            }
        } else {
            if ($this->buttonIsPresent($button)) {
                $this->addVerificationMessage("Button $button is present on the page on $diffScope");
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>"Account Sharing Options" functionality is enabled only on Default Config scope.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Configuration - General, expand Single-Store Mode fieldset and enable Single Store Mode</p>
     * <p>3.Select "Default Config" on the scope switcher</p>
     * <p>4.Go to Customer - Customer Configuration.</p>
     * <p>5.Check for "Account Sharing Options" fieldset.</p>
     * <p>Expected result: </p>
     * <p>"Account Sharing Options" fieldset is displayed.</p>
     * <p>6.Change the scope to "Main Website" or "Default Store View".</p>
     * <p>Expected result: </p>
     * <p>"Account Sharing Options" fieldset is not displayed.</p>
     * <p>7.Repeat previous steps with disabled Single-Store Mode.</p>
     * <p>Expected result:</p>
     * <p>The same results as in previous case.</p>
     *
     * @dataProvider diffScopeSingleStoreDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6188
     * @author Tatyana_Gonchar
     */
    function verificationAccountSharingOptions($diffScope, $singleStoreModeEnabler)
    {
        $config = $this->loadDataSet('SingleStoreMode', $singleStoreModeEnabler);
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->systemConfigurationHelper()->changeConfigurationScope('current_configuration_scope', $diffScope);
        $this->systemConfigurationHelper()->openConfigurationTab('customers_customer_configuration');
        $fieldset = 'account_sharing_options';
        if ($diffScope == 'Default Config') {
            if (!$this->controlIsPresent('fieldset', $fieldset)) {
                $this->addVerificationMessage("Fieldset $fieldset is not present on the page on $diffScope");
            }
        } else {
            if ($this->controlIsPresent('fieldset', $fieldset)) {
                $this->addVerificationMessage("Fieldset $fieldset is present on the page on $diffScope");
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>"Price" fieldset is displayed only on Default Config scope.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>3.Go to System - Configuration - General, expand Single-Store Mode fieldset and enable Single Store Mode</p>
     * <p>4.Select "Default Config" on the scope switcher</p>
     * <p>5.Go to Catalog - Catalog.</p>
     * <p>6.Check for "Price" fieldset.</p>
     * <p>Expected result: </p>
     * <p>"Price" fieldset is displayed.</p>
     * <p>7.Change the scope to "Main Website" or "Default Store View".</p>
     * <p>Expected result: </p>
     * <p>"Price" fieldset is not displayed.</p>
     * <p>8.Repeat previous steps with disabled Single-Store Mode.</p>
     * <p>Expected result:</p>
     * <p>The same results as in previous case.</p>
     *
     * @dataProvider diffScopeSingleStoreDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6189
     * @author Tatyana_Gonchar
     */
    function verificationCatalogPrice($diffScope, $singleStoreModeEnabler)
    {
        $config = $this->loadDataSet('SingleStoreMode', $singleStoreModeEnabler);
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->systemConfigurationHelper()->changeConfigurationScope('current_configuration_scope', $diffScope);
        $this->systemConfigurationHelper()->openConfigurationTab('catalog_catalog');
        $fieldset = 'price';
        if ($diffScope == 'Default Config') {
            if (!$this->controlIsPresent('fieldset', $fieldset)) {
                $this->addVerificationMessage("Fieldset $fieldset is not present on the page on $diffScope");
            }
        } else {
            if ($this->controlIsPresent('fieldset', $fieldset)) {
                $this->addVerificationMessage("Fieldset $fieldset is present on the page on $diffScope");
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>"Debug" fieldset is displayed only on Main Website and Default Store View scopes.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Configuration - General, expand Single-Store Mode fieldset and enable Single Store Mode</p>
     * <p>3.Select "Main Website" on the scope switcher</p>
     * <p>4.Go to Advanced - Developer.</p>
     * <p>5.Check for "Debug" fieldset.</p>
     * <p>Expected result: </p>
     * <p>"Debug" fieldset is displayed.</p>
     * <p>6.Change the scope to "Default Store View" </p>
     * <p>Expected result: </p>
     * <p>"Debug" fieldset is displayed.</p>
     * <p>7.Change the scope to "Default Config" </p>
     * <p>Expected result: </p>
     * <p>"Debug" fieldset is not displayed.</p>
     * <p>8.Repeat previous steps with disabled Single-Store Mode.</p>
     * <p>Expected result:</p>
     * <p>The same results as in previous case.</p>
     *
     * @dataProvider diffScopeSingleStoreDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6190
     * @author Tatyana_Gonchar
     */
    function verificationDebugOptions($diffScope, $singleStoreModeEnabler)
    {
        $config = $this->loadDataSet('SingleStoreMode', $singleStoreModeEnabler);
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->systemConfigurationHelper()->changeConfigurationScope('current_configuration_scope', $diffScope);
        $this->systemConfigurationHelper()->openConfigurationTab('advanced_developer');
        $fieldset = 'debug';
        if (($diffScope == 'Main Website') || ($diffScope == 'Default Store View')) {
            if (!$this->controlIsPresent('fieldset', $fieldset)) {
                $this->addVerificationMessage("Fieldset $fieldset is not present on the page on $diffScope");
            }
        } else {
            if ($this->controlIsPresent('fieldset', $fieldset)) {
                $this->addVerificationMessage("Fieldset $fieldset is present on the page on $diffScope");
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Hints for fields are enabled.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Configuration - General, expand Single-Store Mode fieldset and enable Single Store Mode</p>
     * <p>3.Open required tab and fieldset and check hints </p>
     * <p>Expected result: </p>
     * <p>Hints are displayed</p>
     * <p>4.Repeat previous steps with disabled Single-Store Mode.</p>
     * <p>Expected result:</p>
     * <p>The same results as in previous case.</p>
     *
     * @dataProvider singleStoreModeEnablerDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6201
     * @author Tatyana_Gonchar
     */
    function verificationHints($singleStoreModeEnabler)
    {
        $config = $this->loadDataSet('SingleStoreMode', $singleStoreModeEnabler);
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $storeView = $this->_getControlXpath('pageelement', 'store_view_hint');
        $globalView = $this->_getControlXpath('pageelement', 'global_view_hint');
        $websiteView = $this->_getControlXpath('pageelement', 'website_view_hint');
        $this->admin('system_configuration');
        $tabs = $this->getCurrentUimapPage()->getMainForm()->getAllTabs();
        foreach ($tabs as $tab => $value) {
            $uimapFields = array();
            $this->openTab($tab);
            $uimapFields[self::FIELD_TYPE_MULTISELECT] = $value->getAllMultiselects();
            $uimapFields[self::FIELD_TYPE_DROPDOWN] = $value->getAllDropdowns();
            $uimapFields[self::FIELD_TYPE_INPUT] = $value->getAllFields();
            foreach ($uimapFields as $element) {
                foreach ($element as $name => $xpath) {
                    if ((!$this->isElementPresent($xpath . $storeView)) && (!$this->isElementPresent($xpath . $globalView)) &&
                        (!$this->isElementPresent($xpath . $websiteView))) {
                            $this->addVerificationMessage("Element $name is not on the page");
                    }
                }
            }
        }
        $this->assertEmptyVerificationErrors();
    }
}
