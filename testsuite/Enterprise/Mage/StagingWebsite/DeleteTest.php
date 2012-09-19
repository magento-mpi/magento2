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
 * Delete Staging Website tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_StagingWebsite_DeleteTest extends Mage_Selenium_TestCase
{
    protected function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions</p>
     * <p>Create Staging Website</p>
     * <p>Steps:</p>
     * <p>1. Go to system - Content Staging - Staging Websites;</p>
     * <p>2. Press button "Add Staging Website";</p>
     * <p>3. Select Source Website and press button "Continue";</p>
     * <p>4. Specify all settings/options for Staging Website:</p>
     * <p>Staging Website Code, Staging Website Name, Frontend Restriction</p>
     * <p>and Select Original Website Content to be Copied to the Staging Website</p>
     * <p>5. Press button "Create".</p>
     * <p>Expected Results:</p>
     * <p>1. New Staging Website has been created;</p>
     * <p>2. Admin user is redirected to Manage Staging Website page;</p>
     * <p>3. Message "The staging website has been created." appears;</p>
     *
     * @return string $websiteName
     * @test
     */
    public function createWebsite()
    {
        //Data
        $website = $this->loadDataSet('StagingWebsite', 'staging_website');
        //Steps
        $this->navigate('manage_staging_websites');
        $this->stagingWebsiteHelper()->createStagingWebsite($website);
        //Verification
        $this->assertMessagePresent('success', 'success_created_website');

        return $website['general_information']['staging_website_name'];
    }

    /**
     * <p>Test case TL-MAGE-2048</p>
     * <p>Deleting Staging Websites</p>
     * <p>Steps:</p>
     * <p>1. Go to "Manage Stores";</p>
     * <p>2. Open previously created website;</p>
     * <p>3. Press button "Delete Website".</p>
     * <p>Expected Results:</p>
     * <p>1. Website should be deleted;</p>
     *
     * @param string $websiteName
     *
     * @test
     * @depends createWebsite
     */
    public function deleteWebsite($websiteName)
    {
        //Data
        $deleteWebsiteData = array('website_name' => $websiteName);
        //Steps
        $this->navigate('manage_stores');
        $this->assertTrue($this->storeHelper()->deleteStore($deleteWebsiteData), 'Could not delete website');
    }
}
