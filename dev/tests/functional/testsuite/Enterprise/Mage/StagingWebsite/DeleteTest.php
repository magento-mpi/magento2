<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_StagingWebsite
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
