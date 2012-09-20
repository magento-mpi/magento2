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

class Community2_Mage_Store_DisableSingleStoreMode_SystemConfigurationTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Precondition for test:</p>
     * <p>1. Login to backend.</p>
     * <p>2. Navigate to System -> Manage Store.</p>
     * <p>3. Verify that one store-view is created.<p>
     * <p>4. Go to System - Configuration - General and disable Single-Store Mode.</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->admin('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified(array('Default Store View'));
        $config = $this->loadDataSet('SingleStoreMode', 'disable_single_store_mode');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
    }

    /**
     * <p>Scope Selector is displayed is Single Store Mode is disabled.</p>
     * <p>Steps:</p>
     * <p>1.Go to System - Configuration
     * <p>Expected result: </p>
     * <p>Scope Selector is displayed.</p>
     * <p>2.Repeat previous step with all different scope (Main Website, Default Store View).</p>
     * <p>Expected result: </p>
     * <p>Scope Selector is displayed.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6256
     * @author Tatyana_Gonchar
     */
    function verificationScopeSelector()
    {
        $this->admin('system_configuration');
        $this->assertTrue($this->controlIsPresent('fieldset', 'current_configuration_scope'),
            'There is no Scope Selector');
    }

    public function diffScopeDataProvider()
    {
        return array(
            array('Main Website'),
            array('Default Store View'),
            array('Default Config')
        );
    }

    /**
     * <p>"Export Table Rates" functionality is enabled only on Website scope.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Select "Main Website" on the scope switcher</p>
     * <p>3.Go to Sales - Shipping Methods.</p>
     * <p>4.Check for "Table Rates" fieldset.</p>
     * <p>Expected result: </p>
     * <p>"Export CSV" button is displayed.</p>
     * <p>5.Change the scope to "Default Store View" or "Default Config".</p>
     * <p>Expected result: </p>
     * <p>"Export CSV" button is not displayed.</p>
     *
     * @dataProvider diffScopeDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6251
     * @author Tatyana_Gonchar
     */
    function verificationTableRatesExport($diffScope)
    {
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
     * <p>2.Select "Default Config" on the scope switcher</p>
     * <p>3.Go to Customer - Customer Configuration.</p>
     * <p>4.Check for "Account Sharing Options" fieldset.</p>
     * <p>Expected result: </p>
     * <p>"Account Sharing Options" fieldset is displayed.</p>
     * <p>5.Change the scope to "Main Website" or "Default Store View".</p>
     * <p>Expected result: </p>
     * <p>"Account Sharing Options" fieldset is not displayed.</p>
     *
     * @dataProvider diffScopeDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6253
     * @author Tatyana_Gonchar
     */
    function verificationAccountSharingOptions($diffScope)
    {
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
     * <p>2.Select "Default Config" on the scope switcher</p>
     * <p>3.Go to Catalog - Catalog.</p>
     * <p>4.Check for "Price" fieldset.</p>
     * <p>Expected result: </p>
     * <p>Price" fieldset is displayed.</p>
     * <p>5.Change the scope to "Main Website" or "Default Store View".</p>
     * <p>Expected result: </p>
     * <p>"Price" fieldset is not displayed.</p>
     *
     * @dataProvider diffScopeDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6252
     * @author Tatyana_Gonchar
     */
    function verificationCatalogPrice($diffScope)
    {
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
     * <p>Debug" fieldset is displayed only on Main Website and Default Store View scopes.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Select "Main Website" on the scope switcher</p>
     * <p>3.Go to Advanced - Developer.</p>
     * <p>4.Check for "Debug" fieldset.</p>
     * <p>Expected result: </p>
     * <p>"Debug" fieldset is displayed.</p>
     * <p>5.Change the scope to "Default Store View" </p>
     * <p>Expected result: </p>
     * <p>"Debug" fieldset is displayed.</p>
     * <p>6.Change the scope to "Default Config" </p>
     * <p>Expected result: </p>
     * <p>"Debug" fieldset is not displayed.</p>
     *
     * @dataProvider diffScopeDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6254
     * @author Tatyana_Gonchar
     */
    function verificationDebugOptions($diffScope)
    {
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
     * <p>Hints for fields are enabled if Single Store Mode disabled.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Open required tab and fieldset and check hints </p>
     * <p>Expected result: </p>
     * <p>Hints are displayed</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6255
     * @author Tatyana_Gonchar
     */
    function verificationHints()
    {
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