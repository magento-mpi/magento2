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
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Configuration</p>
     */
    protected function assertPreConditions() // Preconditions
    {
        $this->loginAdminUser(); // Log-in
        $this->navigate('system_configuration'); // Navigate to System -> Configuration
    }

    /**
     * <p>Custom Admin URL is equal to Base URL</p>
     * <p>Steps:</p>
     * <p>1. Log in to  backend;</p>
     * <p>2. Go to System Configuration;</p>
     * <p>3. Open Admin - Admin Base URL fieldset;</p>
     * <p>4. Set Use Custom Admin URL to Yes;</p>
     * <p>5. Enter URL that is equal to Base URL to Custom Admin URL field;</p>
     * <p>6. Save configuration;</p>
     * <p>Expected result:</p>
     * <p> Configuration is saved without errors. Admin is reloaded using specified Custom Admin URL. All internal
     * Admin links are using Custom Admin URL. Actually in this case no visual changes should be introduced.;</p>
     *
     *
     * @test
     * @TestlinkId    TL-MAGE-4667
     */
    public function customAdminUrlIsEqual()
    {
        $this->markTestIncomplete('Incomplete test with wrong logic');
        //Data
        $CustomUrlData = $this->loadDataSet('AdminBaseUrl', 'admin_custom_url_is_equal');
        $this->addParameter('customUrl', '1');
        //Steps
        $this->systemConfigurationHelper()->configure($CustomUrlData);
        //Verifying
        $this->assertTrue($this->verifyForm($CustomUrlData, 'advanced_admin', array('use_custom_admin_path')),
            'Unexpected value in field');
    }
}
?>
