<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AdminBaseUrl
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin Base Url functionality tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_AdminBaseUrl_CustomAdminUrlTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->markTestIncomplete('Incomplete test with wrong logic');
        $this->loginAdminUser();
        $this->navigate('system_configuration');
    }

    /**
     * <p>Custom Admin URL is equal to Base URL</p>
     *
     * @test
     * @TestlinkId TL-MAGE-4667
     */
    public function customAdminUrlIsEqual()
    {
        //Data
        $customUrlData = $this->loadDataSet('AdminBaseUrl', 'admin_custom_url_is_equal');
        $this->addParameter('customUrl', '1');
        //Steps
        $this->systemConfigurationHelper()->configure($customUrlData);
        //Verifying
        $this->assertTrue($this->verifyForm($customUrlData, 'advanced_admin', array('use_custom_admin_path')),
            'Unexpected value in field');
    }
}
