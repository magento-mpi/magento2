<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_SystemConfiguration
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */

class Core_Mage_SystemConfiguration_CheckoutTabTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Disable Single Store Mode</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->admin('system_configuration');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/disable_single_store_mode');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->admin('system_configuration');
    }

    /**
     * <p>Checkout tab is displayed on the all Scopes</p>
     *
     * @dataProvider diffConfigScopeDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6236
     */
    public function verificationCheckoutTab($diffScope)
    {
        $this->selectStoreScope('dropdown', 'current_configuration_scope', $diffScope);
        $this->assertTrue($this->controlIsPresent('tab', 'sales_checkout'),
            "'Checkout' tab is not present on the page if Scope is $diffScope");
    }

    public function diffConfigScopeDataProvider()
    {
        return array(
            array('Main Website'),
            array('Default Store View'),
            array('Default Config')
        );
    }
}
