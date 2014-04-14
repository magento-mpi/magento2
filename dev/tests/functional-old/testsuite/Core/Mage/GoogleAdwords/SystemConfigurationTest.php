<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Core_Mage_GoogleAdwords_SystemConfigurationTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Login as admin user.</p>
     * <p>Navigate to System -> Configurations.</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
    }

    /**
     * @test
     * @group goinc
     */
    public function checkConfigSaveSuccess()
    {
        $this->systemConfigurationHelper()->configure('GoogleApi/google_adwords_enable');
    }

    /**
     * @test
     * @group goinc
     */
    public function checkConfigSaveColorFail()
    {
        $invalidColor = 'not-valid';
        $this->openTab('general_google_api');

        $data = $this->loadDataSet('GoogleApi', 'google_adwords_enable/tab_1/configuration/google_adwords');
        $data['conversion_color'] = $invalidColor;
        $this->fillFieldset($data, 'google_adwords');
        $this->clickButton('save_config', false);
        $this->addParameter('color', $invalidColor);
        $this->assertMessagePresent('error', 'error_save_google_adwords_color');
    }

    /**
     * @test
     * @group goinc
     */
    public function checkConfigSaveConversionIdFail()
    {
        $conversionId = 'some-string';
        $this->openTab('general_google_api');

        $data = $this->loadDataSet('GoogleApi', 'google_adwords_enable/tab_1/configuration/google_adwords');
        $data['conversion_id'] = $conversionId;
        $this->fillFieldset($data, 'google_adwords');
        $this->clickButton('save_config', false);
        $this->addParameter('conversionId', $conversionId);
        $this->assertMessagePresent('error', 'error_save_google_adwords_conversion_id');
    }
}
