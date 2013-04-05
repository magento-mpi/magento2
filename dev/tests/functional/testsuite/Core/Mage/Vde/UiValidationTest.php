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
class Core_Mage_Vde_UiValidationTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/disable_secret_key');
    }

    public function tearDownAfterTestClass()
    {
        $this->admin('system_configuration');
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
        $this->addParameter('themeId','1');
        $this->admin('vde_design');
        //Verifying
        $this->assertFalse($this->controlIsPresent('pageelement', 'navigation_menu_items'));
        $this->assertTrue($this->controlIsPresent('dropdown', 'page_selector'),
            'Page selector is not present on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'view_options'),
            'View options dropdown is not present on the page');
        $this->assertTrue($this->buttonIsPresent('view_layout'), 'View Layout button is not present on the page');
        $this->assertTrue($this->buttonIsPresent('back'), 'Back button is not present on the page');
        $this->assertTrue($this->controlIsPresent('fieldset', 'iframe'), 'iFrame is not present on the page');
    }
}
