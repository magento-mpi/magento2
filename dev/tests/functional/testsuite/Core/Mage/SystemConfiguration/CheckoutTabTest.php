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
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->admin('system_configuration');
    }

    /**
     * <p>Checkout tab is displayed on the all Scopes</p>
     * <p>Preconditions:</p>
     * <p>Login to backend.</p>
     * <p>Go to System > Configuration</p>
     * <p>Steps to reproduce:</p>
     * <p>1. Select "Main Website" or "Default Store View" or "Default Config" on the scope switcher</p>
     * <p>Expected result:</p>
     * <p>Checkout tab is displayed for the every scope.</p>
     *
     * @dataProvider diffConfigScopeDataProvider
     *
     * @test
     * @TestlinkId TL-MAGE-6236
     */
    function verificationCheckoutTab($diffScope)
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
