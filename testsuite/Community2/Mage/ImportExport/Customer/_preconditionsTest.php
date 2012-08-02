<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Export
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_ImportExport_preconditionTest extends Mage_Selenium_TestCase
{
    /**
     * <p>set preconditions to run tests </p>
     * <p>System settings:</p>
     * <p>Secure Key is disabled</p>
     * <p>HttpOnly cookies is disabled</p>
     *
     * @test
     */
    public function preconditionForTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('General/disable_httponly');
        $this->systemConfigurationHelper()->configure('Advanced/disable_secret_key');
    }
}
