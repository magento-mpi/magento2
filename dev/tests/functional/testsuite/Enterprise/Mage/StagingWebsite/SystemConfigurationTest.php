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

/**
 * Create Website tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_StagingWebsite_SystemConfigurationTest extends Mage_Selenium_TestCase
{
    protected function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Test Case TL-MAGE-2006: Automatically Create Entry Points - negative</p>
     * <p>Steps:</p>
     * <p>1. Go to system - configuration - general - staging websites;</p>
     * <p>2. Set "Automatically Create Entry Points for Staging Websites" to "No";</p>
     * <p>3. Press button "Save Config";</p>
     * <p>4. Navigate to "Content Staging" - "Staging Website";</p>
     * <p>5. Press "Add Staging Website" button;</p>
     * <p>6. Select "Main Website" as Source Website;</p>
     * <p>7. Check that "Base Url" and "Secure Base Url" fields are present.</p>
     * <p>Expected Results:</p>
     * <p>1. "Base Url" and "Secure Base Url" fields are present.</p>
     *
     * @test
     */
    public function doNotCreateEntryPointAuto()
    {
        //Data
        $website = $this->loadDataSet('StagingWebsite', 'staging_website');
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($this->loadDataSet('StagingWebsite',
            'staging_website_disable_auto_entries'));
        $this->navigate('manage_staging_websites');
        $this->clickButton('add_staging_website');
        $this->stagingWebsiteHelper()->fillSettings($website['settings']);
        //Verification
        $this->assertTrue($this->controlIsPresent('field', 'base_url'), 'Base Url field is not present on the page');
        $this->assertTrue($this->controlIsPresent('field', 'secure_base_url'),
            'Secure Base Url field is not present on the page');
    }

    /**
     * <p>Test Case TL-MAGE-2005: Automatically Create Entry Points</p>
     * <p>Steps:</p>
     * <p>1. Go to system - configuration - general - staging websites;</p>
     * <p>2. Set "Automatically Create Entry Points for Staging Websites" to "Yes";</p>
     * <p>3. Write Folder Name for Entry Points to field "Folder Name for Entry Points"(staging);</p>
     * <p>4. Press button "Save Config";</p>
     * <p>5. Navigate to "Content Staging" - "Staging Website";</p>
     * <p>6. Press "Add Staging Website" button;</p>
     * <p>7. Select "Main Website" as Source Website;</p>
     * <p>8. Check that "Base Url" and "Secure Base Url" fields are present.</p>
     * <p>Expected Results:</p>
     * <p>1. "Base Url" and "Secure Base Url" fields are present.</p>
     *
     * @test
     */
    public function createEntryPointAuto()
    {
        //Data
        $website = $this->loadDataSet('StagingWebsite', 'staging_website');
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($this->loadDataSet('StagingWebsite',
            'staging_website_enable_auto_entries'));
        $this->navigate('manage_staging_websites');
        $this->clickButton('add_staging_website');
        $this->stagingWebsiteHelper()->fillSettings($website['settings']);
        //Verification
        $this->assertFalse($this->controlIsPresent('field', 'base_url'), 'Base Url field is not present on the page');
        $this->assertFalse($this->controlIsPresent('field', 'secure_base_url'),
            'Secure Base Url field is not present on the page');
    }

}