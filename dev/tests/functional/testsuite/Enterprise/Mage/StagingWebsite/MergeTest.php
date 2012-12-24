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
 * Merge Staging Website tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_StagingWebsite_MergeTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('StagingWebsite/staging_website_enable_auto_entries');
    }

    protected function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions</p>
     * <p>Create Staging Website</p>
     *
     * @return string $websiteCode
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
     * <p>Test case TL-MAGE-2013 and TL-MAGE-2025 and TL-MAGE-2026</p>
     * <p>Merge Now</p>
     *
     * @param string $websiteName
     *
     * @test
     * @depends createWebsite
     */
    public function mergeWebsite($websiteName)
    {
        //Data
        $mergeWebsiteData =
            $this->loadDataSet('StagingWebsite', 'merge_website', array('filter_website_name' => $websiteName));
        $withoutMapToData = $this->loadDataSet('StagingWebsite', 'merge_website_without_map',
            array('filter_website_name' => $websiteName));
        $withoutMapToData = $this->clearDataArray($withoutMapToData);
        $backupStarted = $this->loadDataSet('Backups', 'staging_website_backup_started_log',
            array('filter_website_from' => 'Main Website'));
        $backupCompleted = $this->loadDataSet('Backups', 'staging_website_backup_completed_log',
            array('filter_website_from' => 'Main Website'));
        $mergeStarted = $this->loadDataSet('Backups', 'staging_website_merge_started_log',
            array('filter_website_from' => $websiteName,
                  'filter_website_to'   => 'Main Website'));
        $mergeCompleted = $this->loadDataSet('Backups', 'staging_website_merge_completed_log',
            array('filter_website_from' => $websiteName,
                  'filter_website_to'   => 'Main Website'));
        //Steps
        $this->navigate('manage_staging_websites');
        $this->stagingWebsiteHelper()->openStagingWebsite($withoutMapToData['search_website']);
        $this->fillTab($withoutMapToData['general_information'], 'general_information');
        $this->clickButton('merge');
        $this->fillForm($withoutMapToData['merge_configuration']);
        //TL-MAGE-2013 verification
        $alert = $this->getCurrentUimapPage()->findMessage('confirmation_select_website_to_map');
        $this->clickButton('merge_now', false);
        $textAlert = $this->alertText();
        $this->acceptAlert();
        if ($textAlert != $alert) {
            $this->fail('Alert text should be: ' . $alert . ', but actually is: ' . $textAlert);
        }
        //Steps
        $this->navigate('manage_staging_websites');
        $this->addParameter('elementTitle', $websiteName);
        $this->stagingWebsiteHelper()->mergeWebsite($mergeWebsiteData);
        //Verification
        $this->assertMessagePresent('success', 'success_merged_website');
        $this->addParameter('websiteName', $mergeWebsiteData['search_website']['filter_website_name']);
        $this->addParameter('latestEvent', 'Instant Merger');
        $this->search(array('filter_website_name' => $websiteName), 'staging_websites_grid');
        //Search is needed here for getting to the page with the needed row
        $this->assertTrue($this->controlIsPresent('pageelement', 'latest_event'));
        //Verification of TL-MAGE-2025
        $this->navigate('manage_staging_operations_log');
        $this->searchAndOpen($backupStarted, 'staging_operations_log_grid');
        $this->navigate('manage_staging_operations_log');
        $this->searchAndOpen($backupCompleted, 'staging_operations_log_grid');
        //Verification of TL-MAGE-2026
        $this->navigate('manage_staging_operations_log');
        $this->searchAndOpen($mergeStarted, 'staging_operations_log_grid');
        $this->navigate('manage_staging_operations_log');
        $this->searchAndOpen($mergeCompleted, 'staging_operations_log_grid');
    }

    /**
     * <p>Test Case TL-MAGE-2014: Schedule Merge and TL-MAGE-2027: Merger Scheduling</p>
     *
     * @param string $websiteName
     *
     * @return string $websiteName
     * @test
     * @depends createWebsite
     */
    public function scheduleMergeWebsite($websiteName)
    {
        //Data
        $mergeWebsiteData = $this->loadDataSet('StagingWebsite', 'schedule_merge_website',
            array('filter_website_name' => $websiteName));
        $mergeSiteDataWODate = $mergeWebsiteData;
        $mergeSiteDataWODate['schedule_merge']['schedule_merge_input'] = '';
        $mergeSiteDataWODate = $this->clearDataArray($mergeSiteDataWODate);
        $scheduleCompleted = $this->loadDataSet('Backups', 'staging_website_schedule_completed_log',
            array('filter_website_from' => $websiteName,
                  'filter_website_to'   => 'Main Website'));
        //Steps
        $this->navigate('manage_staging_websites');
        $this->addParameter('elementTitle', $websiteName);
        $this->stagingWebsiteHelper()->mergeWebsite($mergeSiteDataWODate);
        $this->assertMessagePresent('validation', 'empty_required_field_merge_date');
        $this->navigate('manage_staging_websites');
        $this->stagingWebsiteHelper()->mergeWebsite($mergeWebsiteData);
        //Verification
        $this->assertMessagePresent('success', 'success_scheduled_merge');
        $this->addParameter('websiteName', $mergeWebsiteData['search_website']['filter_website_name']);
        $this->addParameter('latestEvent', 'Merger Scheduling');
        $this->search(array('filter_website_name' => $websiteName), 'staging_websites_grid');
        $this->assertTrue($this->controlIsPresent('pageelement', 'latest_event'));
        //Verification of TL-MAGE-2027
        $this->navigate('manage_staging_operations_log');
        $this->searchAndOpen($scheduleCompleted, 'staging_operations_log_grid');

        return $websiteName;
    }

    /**
     * <p>Test Case TL-MAGE-2015: Unschedule Merge and TL-MAGE-2028: Merger Unscheduling</p>
     *
     * @param string $websiteName
     *
     * @test
     * @depends scheduleMergeWebsite
     */
    public function unscheduleMergeWebsite($websiteName)
    {
        //Data
        $mergeWebsiteData = $this->loadDataSet('StagingWebsite', 'schedule_merge_website',
            array('filter_website_name' => $websiteName));
        $unscheduleCompleted = $this->loadDataSet('Backups', 'staging_website_schedule_completed_log',
            array('filter_website_from' => $websiteName,
                  'filter_website_to'   => 'Main Website'));
        //Steps
        $this->navigate('manage_staging_websites');
        $this->addParameter('elementTitle', $websiteName);
        $this->stagingWebsiteHelper()->openStagingWebsite(array('filter_website_name' => $websiteName));
        $this->saveForm('unschedule_merge');
        //Verification
        $this->assertMessagePresent('success', 'success_unscheduled_merge');
        $this->addParameter('websiteName', $mergeWebsiteData['search_website']['filter_website_name']);
        $this->addParameter('latestEvent', 'Merger Unscheduling');
        $this->search(array('filter_website_name' => $websiteName), 'staging_websites_grid');
        $this->assertTrue($this->controlIsPresent('pageelement', 'latest_event'));
        //Verification of TL-MAGE-2028
        $this->navigate('manage_staging_operations_log');
        $this->searchAndOpen($unscheduleCompleted, 'staging_operations_log_grid');
    }

    /**
     * <p>Test Case: Schedule Merge With Incorrect Date</p>
     *
     * @test
     */
    public function scheduleMergeWebsiteWithIncorrectDate()
    {
        //Preconditions
        $website = $this->loadDataSet('StagingWebsite', 'staging_website');
        $this->navigate('manage_staging_websites');
        $this->stagingWebsiteHelper()->createStagingWebsite($website);
        $this->assertMessagePresent('success', 'success_created_website');
        //Data
        $mergeWebsiteData = $this->loadDataSet('StagingWebsite', 'schedule_merge_website',
            array('filter_website_name'     => $website['general_information']['staging_website_name'],
                  'schedule_merge_input'    => $this->generate('string', 6, ':alnum:')));
        //Steps
        $this->navigate('manage_staging_websites');
        $this->addParameter('elementTitle', $website['general_information']['staging_website_name']);
        $this->stagingWebsiteHelper()->mergeWebsite($mergeWebsiteData);
        $this->assertMessagePresent('error', 'error_invalid_date');
    }
}
