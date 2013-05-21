<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Core_Mage_GoogleOptimizer_CategoryTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();

        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('sales_google_api');
        $this->systemConfigurationHelper()->expandFieldSet('google_analytics');
        $this->fillField('google_verification_code', '');
        $this->clickButton('save_config');
        $this->assertMessagePresent('success', 'success_saved_config');
    }

    /**
     * @test
     * @group goinc2
     */
    public function checkBehaviorOnCreate()
    {
        $this->navigate('manage_categories', false);
        //$this->categoryHelper()->checkCategoriesPage();
    }

    /**
     * @test
     * @group goinc2
     */
    public function checkBehaviorOnUpdate()
    {
    }

    /**
     * @test
     * @group goinc2
     */
    public function checkBehaviorOnDelete()
    {
    }

    /**
     * @test
     * @group goinc2
     */
    public function checkBehaviorIfDisabled()
    {
    }
}
