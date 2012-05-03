<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

    protected function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions</p>
     * <p>Create Staging Website</p>
     *
     * <p>Steps:</p>
     * <p>1. Go to system - Content Staging - Staging Websites;</p>
     * <p>2. Press button "Add Staging Website";</p>
     * <p>3. Select Source Website and press button "Continue";</p>
     * <p>4. Specify all settings/options for Staging Website:</p>
     * <p>Staging Website Code, Staging Website Name, Frontend Restriction</p>
     * <p>and Select Original Website Content to be Copied to the Staging Website</p>
     * <p>5. Press button "Create".</p>
     *
     * <p>Expected Results:</p>
     * <p>1. New Staging Website has been created;</p>
     * <p>2. Admin user is redirected to Manage Staging Website page;</p>
     * <p>3. Message "The staging website has been created." appears;</p>
     *
     * @return string $websiteCode
     *
     * @test
     */
    public function createWebsite()
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('staging_website_enable_auto_entries');
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
     * <p>Steps:</p>
     * <p>1. Go to system - Content Staging - Staging Websites;</p>
     * <p>2. Open previously created website;</p>
     * <p>3. Press button "Merge";</p>
     * <p>4. Do not select website to map to;</p>
     * <p>5. Press button "Merge Now"</p>
     * <p>6. Open previously created website;</p>
     * <p>7. Press button "Merge";</p>
     * <p>8. Select Website to map;</p>
     * <p>9. Check "Create a backup" checkbox;</p>
     * <p>10. Select Items to be Merged;</p>
     * <p>11. Press button "Merge Now"</p>
     *
     * <p>Expected Results:</p>
     * <p>1. Message "Please, select website to map" message appears;</p>
     * <p>2. Merging process is carried out immediately;</p>
     * <p>3. Admin user is redirected to Manage Staging Website page;</p>
     * <p>4. Message "The staging website has been merged." appears;</p>
     * <p>5. In "Latest Event" column on Manage Staging Websites page shown value "Instant Merger".</p>
     * <p>6. On Staging Operations Log page record is added with info: "Action - Backup, Websites from - target website,
     * Websites to - empty, Result - Started, Completed"</p>
     * <p>7. On Staging Operations Log page record is added with info: "Action - Instant Merger,
     * Websites from - target website, Websites to - staging website, Result - Started, Completed"</p>
     *
     * @depends createWebsite
     * @param string $websiteName
     *
     * @test
     */
    public function mergeWebsite($websiteName)
    {
        //Data
        $mergeWebsiteData = $this->loadDataSet(
            'StagingWebsite', 'merge_website', array('filter_website_name' => $websiteName));
        $withoutMapToData = $this->loadDataSet(
            'StagingWebsite', 'merge_website_without_map', array('filter_website_name' => $websiteName));
        $withoutMapToData = $this->clearDataArray($withoutMapToData);
        $backupStarted = $this->loadDataSet('Backups', 'staging_website_backup_started_log',
            array('filter_website_from' => 'Main Website'));
        $backupCompleted = $this->loadDataSet('Backups', 'staging_website_backup_completed_log',
            array('filter_website_from' => 'Main Website'));
        $mergeStarted = $this->loadDataSet('Backups', 'staging_website_merge_started_log',
            array('filter_website_from' => $websiteName,
                  'filter_website_to' => 'Main Website'));
        $mergeCompleted = $this->loadDataSet('Backups', 'staging_website_merge_completed_log',
            array('filter_website_from' => $websiteName,
                  'filter_website_to' => 'Main Website'));
        //Steps
        $this->navigate('manage_staging_websites');
        $this->stagingWebsiteHelper()->openStagingWebsite($withoutMapToData['search_website']);
        $this->fillTab($withoutMapToData['general_information'], 'general_information');
        $this->clickButton('merge');
        $this->fillForm($withoutMapToData['merge_configuration']);
        //TL-MAGE-2013 verification
        $alert = $this->getCurrentUimapPage()->findMessage('confirmation_select_website_to_map');
        $this->clickButton('merge_now', false);
        if ($this->isAlertPresent()) {
            $text = $this->getAlert();
            if ($text != $alert) {
                $this->fail('Alert text should be: ' . $alert . ', but actually is: ' . $text);
            }
        } else {
            $this->fail('Alert "'. $alert . '" is not present');
        }
        //Steps
        $this->navigate('manage_staging_websites');
        $this->addParameter('elementTitle', $websiteName);
        $this->stagingWebsiteHelper()->mergeWebsite($mergeWebsiteData);
        //Verification
        $this->assertMessagePresent('success', 'success_merged_website');
        $this->addParameter('websiteName', $mergeWebsiteData['search_website']['filter_website_name']);
        $this->addParameter('latestEvent', 'Instant Merger');
        $this->search(array('filter_website_name' => $websiteName));
        //Search is needed here for getting to the page with the needed row
        $this->assertTrue($this->controlIsPresent('pageelement', 'latest_event'));
        //Verification of TL-MAGE-2025
        $this->navigate('manage_staging_operations_log');
        $this->stagingLogHelper()->openLog($backupStarted);
        $this->navigate('manage_staging_operations_log');
        $this->stagingLogHelper()->openLog($backupCompleted);
        //Verification of TL-MAGE-2026
        $this->navigate('manage_staging_operations_log');
        $this->stagingLogHelper()->openLog($mergeStarted);
        $this->navigate('manage_staging_operations_log');
        $this->stagingLogHelper()->openLog($mergeCompleted);
    }

    /**
     * <p>Test Case TL-MAGE-2014: Schedule Merge and TL-MAGE-2027: Merger Scheduling</p>
     *
     * <p>Steps:</p>
     * <p>1. Go to system - Content Staging - Staging Websites;</p>
     * <p>2. Open previously created website;</p>
     * <p>3. Press button "Merge";</p>
     * <p>4. Press button "Schedule Merge";</p>
     * <p>5. Open previously created website;</p>
     * <p>6. Press button "Merge";</p>
     * <p>7. Select Website to map;</p>
     * <p>8. Check "Create a backup" checkbox;</p>
     * <p>9. Select Items to be Merged;</p>
     * <p>10. Fill in merge schedule fields with future date;</p>
     * <p>11. Press button "Schedule Merge"</p>
     *
     * <p>Expected Results:</p>
     * <p>1. Message "empty_required_field_merge_date" appears;</p>
     * <p>2. Merging process is not carried out immediately;</p>
     * <p>3. Admin user is redirected to Manage Staging Website page;</p>
     * <p>4. Message "The staging website has been scheduled to merge." appears;</p>
     * <p>5. In "Latest Event" column on Manage Staging Websites page shown value "Merger Scheduling".</p>
     * <p>6. On Staging Operations Log page record is added with info: "Action - Merger Scheduling,
     * Websites from - target website, Websites to - staging website, Result - Completed"</p>
     *
     * @depends createWebsite
     * @param string $websiteName
     *
     * @return string $websiteName
     *
     * @test
     */
    public function scheduleMergeWebsite($websiteName)
    {
        //Data
        $mergeWebsiteData = $this->loadDataSet(
            'StagingWebsite', 'schedule_merge_website', array('filter_website_name' => $websiteName));
        $mergeWebsiteDataWODate = $mergeWebsiteData;
        $mergeWebsiteDataWODate['schedule_merge']['schedule_merge_input'] = '';
        $mergeWebsiteDataWODate = $this->clearDataArray($mergeWebsiteDataWODate);
        $scheduleCompleted = $this->loadDataSet('Backups', 'staging_website_schedule_completed_log',
            array('filter_website_from' => $websiteName,
                  'filter_website_to' => 'Main Website'));
        //Steps
        $this->navigate('manage_staging_websites');
        $this->addParameter('elementTitle', $websiteName);
        $this->stagingWebsiteHelper()->mergeWebsite($mergeWebsiteDataWODate);
        $this->assertMessagePresent('validation', 'empty_required_field_merge_date');
        $this->navigate('manage_staging_websites');
        $this->stagingWebsiteHelper()->mergeWebsite($mergeWebsiteData);
        //Verification
        $this->assertMessagePresent('success', 'success_scheduled_merge');
        $this->addParameter('websiteName', $mergeWebsiteData['search_website']['filter_website_name']);
        $this->addParameter('latestEvent', 'Merger Scheduling');
        $this->search(array('filter_website_name' => $websiteName));
        $this->assertTrue($this->controlIsPresent('pageelement', 'latest_event'));
        //Verification of TL-MAGE-2027
        $this->navigate('manage_staging_operations_log');
        $this->stagingLogHelper()->openLog($scheduleCompleted);

        return $websiteName;
    }

    /**
     * <p>Test Case TL-MAGE-2015: Unschedule Merge and TL-MAGE-2028: Merger Unscheduling</p>
     *
     * <p>Steps:</p>
     * <p>1. Go to system - Content Staging - Staging Websites;</p>
     * <p>2. Open previously scheduled for merge website;</p>
     * <p>3. Press button "Unschedule Merge";</p>
     *
     * <p>Expected Results:</p>
     * <p>1. Merging process is not carried out immediately;</p>
     * <p>2. Admin user is redirected to Manage Staging Website page;</p>
     * <p>3. Message "Staging has been unscheduled." appears;</p>
     * <p>4. In "Latest Event" column on Manage Staging Websites page shown value "Merger Unscheduling".</p>
     * <p>5. On Staging Operations Log page record is added with info: "Action - Merger Unscheduling,
     * Websites from - target website, Websites to - staging website, Result - Completed"</p>
     *
     * @depends scheduleMergeWebsite
     * @param string $websiteName
     *
     * @test
     */
    public function unscheduleMergeWebsite($websiteName)
    {
        //Data
        $mergeWebsiteData = $this->loadDataSet(
            'StagingWebsite', 'schedule_merge_website', array('filter_website_name' => $websiteName));
        $unscheduleCompleted = $this->loadDataSet('Backups', 'staging_website_schedule_completed_log',
            array('filter_website_from' => $websiteName,
                  'filter_website_to' => 'Main Website'));
        //Steps
        $this->navigate('manage_staging_websites');
        $this->addParameter('elementTitle', $websiteName);
        $this->stagingWebsiteHelper()->openStagingWebsite(array('filter_website_name' => $websiteName));
        $this->saveForm('unschedule_merge');
        //Verification
        $this->assertMessagePresent('success', 'success_unscheduled_merge');
        $this->addParameter('websiteName', $mergeWebsiteData['search_website']['filter_website_name']);
        $this->addParameter('latestEvent', 'Merger Unscheduling');
        $this->search(array('filter_website_name' => $websiteName));
        $this->assertTrue($this->controlIsPresent('pageelement', 'latest_event'));
        //Verification of TL-MAGE-2028
        $this->navigate('manage_staging_operations_log');
        $this->stagingLogHelper()->openLog($unscheduleCompleted);
    }

    /**
     * <p>Test Case: Schedule Merge With Incorrect Date</p>
     *
     * <p>Preconditions;</p>
     * <p>1. Staging website is created;</p>
     * <p>Steps:</p>
     * <p>1. Go to System - Content Staging - Staging Websites;</p>
     * <p>2. Open previously created website;</p>
     * <p>3. Press button "Merge";</p>
     * <p>4. Select Website to map;</p>
     * <p>5. Select Items to be Merged;</p>
     * <p>6. Fill in merge schedule fields with incorrect date;</p>
     * <p>7. Press button "Schedule Merge"</p>
     *
     * <p>Expected Results:</p>
     * <p>1. Merge is not scheduled;</p>
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
