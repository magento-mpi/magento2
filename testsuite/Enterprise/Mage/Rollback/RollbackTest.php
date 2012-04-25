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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        $website = $this->loadData('staging_website');
        //Steps
        $this->navigate('manage_staging_websites');
        $this->stagingWebsiteHelper()->createStagingWebsite($website);
        //Verification
        $this->assertMessagePresent('success', 'success_created_website');

        return $website['general_information']['staging_website_name'];
    }

    /**
     * <p>Preconditions</p>
     *
     * <p>Steps:</p>
     * <p>1. Go to system - Content Staging - Staging Websites;</p>
     * <p>2. Open previously created website;</p>
     * <p>3. Press button "Merge";</p>
     * <p>4. Select Website to map;</p>
     * <p>5. Check "Create a backup" checkbox;</p>
     * <p>6. Select Items to be Merged;</p>
     * <p>7. Press button "Merge Now"</p>
     *
     * <p>Expected Results:</p>
     * <p>1. Merging process is carried out immediately;</p>
     * <p>2. Admin user is redirected to Manage Staging Website page;</p>
     * <p>3. Message "The staging website has been merged." appears;</p>
     * <p>4. In "Latest Event" column on Manage Staging Websites page shown value "Instant Merger".</p>
     *
     * @depends createWebsite
     * @param string $websiteName
     *
     * @test
     */
    public function mergeWebsite($websiteName)
    {
        //Data
        $mergeWebsiteData = $this->loadData('merge_website', array('filter_website_name' => $websiteName));
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
     * <p>Steps:</p>
     * <p>1. Go to system - content staging - backups;</p>
     * <p>2. Open any Backup;</p>
     * <p>3. Press button "Rollback";</p>
     * <p>4. Go to "Rollback" tab;</p>
     * <p>5. Define elements for rollback;</p>
     * <p>6. Press button "Rollback".</p>
     *
     * <p>Expected Results:</p>
     * <p>After step 3:</p>
     * <p>1. Admin user is stayed on Backup page;</p>
     * <p>3. Message "There are no items selected for rollback." appears;</p>
     *
     * <p>After step 6:</p>
     * <p>1. Admin user is stayed on Backup page;</p>
     * <p>2. Message "The master website has been restored." appears;</p>
     * <p>3. In "Latest Event" column on Manage Staging Websites page shown value "Rollback";</p>
     * <p>4. Master website has been restored</p>
     * <p>5. On Staging Operations Log page record is added with info: "Action - Rollback, Websites from - target website, Websites to - empty, Result - Started, Completed"</p>
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
        $buff = $this->loadData('rollback_backup', array('filter_website_name' => 'Main Website'));
        $rollbackData = $buff;
        unset($buff['items_to_rollback']);
        $noRollbackData = $buff;
        $rollbackStarted = $this->loadData('staging_website_rollback_started_log',
                                            array('filter_website_from' => 'Main Website'));
        $rollbackCompleted = $this->loadData('staging_website_rollback_completed_log',
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
        $this->search(array('filter_website_name' => $websiteName));
        $this->assertTrue($this->controlIsPresent('pageelement', 'latest_event'));
        //Verification of TL-MAGE-2029
        $this->navigate('manage_staging_operations_log');
        $this->stagingLogHelper()->openLog($rollbackStarted);
        $this->navigate('manage_staging_operations_log');
        $this->stagingLogHelper()->openLog($rollbackCompleted);
    }

    /**
     * <p>Test Case TL-MAGE-2023: Deleting Backup</p>
     *
     * <p>Steps:</p>
     * <p>1. Go to system - content staging - backups;</p>
     * <p>2. Open any Backup;</p>
     * <p>3. Press button "Delete";</p>
     * <p>4. Press button "OK";</p>
     *
     * <p>Expected Results:</p>
     * <p>1. Backup is deleted;</p>
     * <p>3. Admin user is redirected to Manage Backups page.</p>
     *
     * @depends mergeWebsite
     *
     * @test
     */
    public function deleteBackup()
    {
        //Data
        $searchWebsite = $this->loadData('rollback_backup', array('filter_website_name' => 'Main Website'));
        unset($searchWebsite['items_to_rollback']);
        //Steps
        $this->navigate('manage_backups');
        $this->rollbackHelper()->deleteBackup($searchWebsite['search_website']);
        $this->validatePage('manage_backups');
    }

}
