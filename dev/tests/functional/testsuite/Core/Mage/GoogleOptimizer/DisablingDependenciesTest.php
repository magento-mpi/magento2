<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Core_Mage_GoogleOptimizer_DisablingDependenciesTest extends Mage_Selenium_TestCase
{
    public function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * @test
     * @group goinc
     */
    public function checkThatGoogleExperimentIsDisabledIfGoogleAnalyticsIsDisabled()
    {
        // Enable 'GoogleExperiment'
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GoogleApi/content_experiments_enable');

        // Disable 'GoogleAnalytics'
        $this->systemConfigurationHelper()->configure('GoogleApi/google_analytics_disable');

        // Check result
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->clickButton('add_root_category', false);
        $this->pleaseWait();
        $this->assertFalse($this->controlIsPresent('tab', 'google_experiment'), 'Element should not be present.');
    }

    /**
     * @test
     * @group goinc
     */
    public function checkThatGoogleExperimentIsDisabledIfGoogleAnalyticsAccountNumberIsEmpty()
    {
        // Enable 'GoogleExperiment'
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GoogleApi/content_experiments_enable');

        // Delete 'AccountNumber'
        $this->systemConfigurationHelper()->configure('GoogleApi/account_number_delete');

        // Check result
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->clickButton('add_root_category', false);
        $this->pleaseWait();
        $this->assertFalse($this->controlIsPresent('tab', 'google_experiment'), 'Element should not be present.');
    }
}
