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
class Community2_Mage_Vde_UiValidationTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/disable_secret_key');
    }

    protected function tearDownAfterTest()
    {
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/enable_secret_key');
        $this->logoutAdminUser();
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-6501
     * @author iuliia.babenko
     */
    public function uiValidationTest()
    {
        //Step
        $this->admin('vde_design');
        //Verifying
        $this->assertTrue($this->controlIsPresent('dropdown', 'page_selector'),
            'Page selector is not present on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'view_options'),
            'View options dropdown is not present on the page');
        $this->assertTrue($this->buttonIsPresent('view_layout'), 'View Layout button is not present on the page');
        $this->assertTrue($this->buttonIsPresent('compact_log'), 'Compact Log button is not present on the page');
        $this->assertTrue($this->buttonIsPresent('quit'), 'Quit button is not present on the page');
        $this->assertTrue($this->controlIsPresent('fieldset', 'iframe'), 'iFrame is not present on the page');
    }
}
