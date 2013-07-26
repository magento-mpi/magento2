<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Rollback
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rollback backup tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_Rollback_RollbackTest extends Mage_Selenium_TestCase
{
    protected function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    /**
     *
     * @return string $websiteCode
     *
     * @test
     */
    public function createWebsite()
    {
        $this->markTestIncomplete("Enterprise_Staging is obsolete. The tests should be refactored.");
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('StagingWebsite/staging_website_enable_auto_entries');
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
     *
     * @depends createWebsite
     * @param string $websiteName
     *
     * @test
     */
    public function mergeWebsite($websiteName)
    {
        $this->markTestIncomplete("Enterprise_Staging is obsolete. The tests should be refactored.");
        //Data
        $mergeWebsiteData = $this->loadDataSet(
            'StagingWebsite', 'merge_website', array('filter_website_name' => $websiteName));
        //Steps
        $this->navigate('manage_staging_websites');
        $this->addParameter('elementTitle', $websiteName);
        $this->stagingWebsiteHelper()->mergeWebsite($mergeWebsiteData);
        //Verification
        $this->assertMessagePresent('success', 'success_merged_website');
        $this->addParameter('websiteName', $mergeWebsiteData['search_website']['filter_website_name']);
        $this->addParameter('latestEvent', 'Instant Merger');
        $this->assertTrue($this->controlIsPresent('pageelement', 'latest_event'));
    }

    /**
     * <p>Test Case TL-MAGE-2022: Rollback and TL-MAGE-2029: Rollback</p>
     *
     * @depends createWebsite
     * @depends mergeWebsite
     * @param string $websiteName
     *
     * @test
     */
    public function rollbackBackup($websiteName)
    {
        //Data
        $buff = $this->loadDataSet('Backups', 'rollback_backup', array('filter_website_name' => 'Main Website'));
        $rollbackData = $buff;
        unset($buff['items_to_rollback']);
        $noRollbackData = $buff;
        $rollbackStarted = $this->loadDataSet('Backups', 'staging_website_rollback_started_log',
            array('filter_website_from' => 'Main Website'));
        $rollbackCompleted = $this->loadDataSet('Backups', 'staging_website_rollback_completed_log',
            array('filter_website_from' => 'Main Website'));
        //Steps
        $this->navigate('manage_backups');
        $this->rollbackHelper()->rollbackBackup($noRollbackData);
        //Verification
        $this->assertMessagePresent('validation', 'validation_no_items_to_rollback');
        //Steps
        $this->navigate('manage_backups');
        $this->rollbackHelper()->rollbackBackup($rollbackData);
        //Verification
        $this->assertMessagePresent('success', 'success_website_restored');
        $this->navigate('manage_staging_websites');
        $this->addParameter('websiteName', $websiteName);
        $this->addParameter('latestEvent', 'Rollback');
        $this->search(array('filter_website_name' => $websiteName), 'staging_websites_grid');
        $this->assertTrue($this->controlIsPresent('pageelement', 'latest_event'));
        //Verification of TL-MAGE-2029
        $this->navigate('manage_staging_operations_log');
        $this->searchAndOpen($rollbackStarted, 'staging_operations_log_grid');
        $this->navigate('manage_staging_operations_log');
        $this->searchAndOpen($rollbackCompleted, 'staging_operations_log_grid');
    }

    /**
     * <p>Test Case TL-MAGE-2023: Deleting Backup</p>
     *
     * @depends mergeWebsite
     *
     * @test
     */
    public function deleteBackup()
    {
        //Data
        $searchWebsite = $this->loadDataSet(
            'Backups', 'rollback_backup', array('filter_website_name' => 'Main Website'));
        unset($searchWebsite['items_to_rollback']);
        //Steps
        $this->navigate('manage_backups');
        $this->rollbackHelper()->deleteBackup($searchWebsite['search_website']);
        $this->validatePage('manage_backups');
    }

}
