<?php
/**
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Various
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Core_Mage_Various_SaveConfigurationTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Bug Cover<p/>
     * <p>Verification of MAGETWO-1918:</p>
     * <p>"Use Default" is checked again after saving multiselect attribute config if option does not contain value</p>
     *
     *
     * @test
     * @TestlinkId TL-MAGE-6290
     */
    public function saveMultiselectWithNoSelectedValuesOnStoreView()
    {
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/disable_single_store_mode');
        $this->selectStoreScope('dropdown', 'current_configuration_scope', 'Main Website');
        $this->systemConfigurationHelper()->openConfigurationTab('general_general');
        $this->systemConfigurationHelper()->expandFieldSet('locale_options');
        $this->fillCheckbox('weekend_days_use_default', 'No');
        $this->fillMultiselect('weekend_days', '');
        $this->saveForm('save_config');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_config');
        $this->assertFalse($this->getControlAttribute('checkbox', 'weekend_days_use_default', 'selectedValue'),
            'Use Default checkbox checked');
        $selectedOptions = $this->select($this->getControlElement('multiselect', 'weekend_days'))->selectedLabels();
        $this->assertCount(0, $selectedOptions, 'Some day still selected in Weekend Days multiselect');
    }
}
