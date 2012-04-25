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
 * Create Website tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_StagingWebsite_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * Log in to Backend.
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('staging_website_enable_auto_entries');
    }

    protected function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        //load default application settings
        $this->_configHelper->setApplication(Mage_Selenium_Helper_Config::DEFAULT_APPLICATION);
    }

    /**
     * <p>Test case TL-MAGE-2011 and TL-MAGE-2024:</p>
     * <p>Staging Website Creation</p>
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
     * <p>4. Newly created Staging Website entity can be found in the list of Staging Websites and base URL for this website is automatically created;</p>
     * <p>5. In "Latest Event" column on Manage Staging Websites page shown value "Staging Website Creation";</p>
     * <p>6. Staging Website opens to the newly created automatically URL and includes only data of Source Website selected in Select Original Website Content to be Copied to the Staging Website tab;</p>
     * <p>7. On Staging Operations Log page record is added with info: "Action - Staging Website Creation, Websites from - master website, Websites to - target website, Result - Started, Completed".</p>
     *
     * @return string $websiteCode
     *
     * @test
     */
    public function createWebsite()
    {
        //Data
        $website = $this->loadData('staging_website');
        $newFronendUrl = $this->stagingWebsiteHelper()->buildFrontendUrl(
            $website['general_information']['staging_website_code']);
        $creationStarted = $this->loadData('staging_website_creation_started_log',
                            array('filter_website_from' => 'Main Website'));
        $creationCompleted = $this->loadData('staging_website_creation_completed_log',
                            array('filter_website_from' => 'Main Website',
                                  'filter_website_to' => $website['general_information']['staging_website_name']));
        //Steps
        $this->navigate('manage_staging_websites');
        $this->stagingWebsiteHelper()->createStagingWebsite($website);
        //Verification
        $this->assertMessagePresent('success', 'success_created_website');
        $this->addParameter('websiteName', $website['general_information']['staging_website_name']);
        $this->addParameter('latestEvent', 'Staging Website Creation');
        $this->search(array('filter_website_name' => $website['general_information']['staging_website_name']));
        //search is used for getting on the page with the matched row
        $this->assertTrue($this->controlIsPresent('pageelement', 'latest_event'));
        //Verification on log page
        $this->navigate('manage_staging_operations_log');
        $this->stagingLogHelper()->openLog($creationStarted);
        $this->navigate('manage_staging_operations_log');
        $this->stagingLogHelper()->openLog($creationCompleted);
        $this->_configHelper->setAreaBaseUrl('frontend', $newFronendUrl);
        $this->frontend();

        return $website['general_information']['staging_website_code'];
    }

    /**
     * <p>Test case TL-MAGE-2019</p>
     * <p>Staging website creation - negative</p>
     *
     * <p>Steps:</p>
     * <p>1. Open Content Staging > Staging Websites</p>
     * <p>2. Press button "Add Staging Website";</p>
     * <p>3. Select Source Website and press button "Continue";</p>
     * <p>4. Specify all settings/options for Staging Website:</p>
     * <p>Staging Website Name, Frontend Restriction</p>
     * <p>and Select Original Website Content to be Copied to the Staging Website</p>
     * <p>5. Enter the Staging Website Code that already exists;</p>
     * <p>6. Press button "Create".</p>
     *
     * <p>Expected Results:</p>
     * <p>1. New Staging Website has not been created;</p>
     * <p>2. Message "Website with the same code already exists." appears;</p>
     *
     * @depends createWebsite
     * @param $websiteCode
     *
     * @test
     */
    public function createWebsiteWithExistingCode($websiteCode)
    {
        //Data
        $website = $this->loadData('staging_website', array('staging_website_code' => $websiteCode));
        //Steps
        $this->navigate('manage_staging_websites');
        $this->stagingWebsiteHelper()->createStagingWebsite($website);
        //Verification
        $this->assertMessagePresent('error', 'error_website_with_the_same_code');
    }

    /**
     * <p>Create Website with empty fields</p>
     *
     * <p>Steps:</p>
     * <p>1. Open Content Staging > Staging Websites</p>
     * <p>2. Press button "Add Staging Website";</p>
     * <p>3. Select Source Website and press button "Continue";</p>
     * <p>4. Specify all settings/options for Staging Website, but leave one field empty;</p>
     * <p>5. Press button "Create".</p>
     *
     * <p>Expected Results:</p>
     * <p>1. New Staging Website has not been created;</p>
     * <p>2. Message "This is a required field." or "Website Item Must be checked" appears;</p>
     *
     * @dataProvider createWebsiteEmptyFieldsDataProvider
     * @param string $fieldType
     * @param string $emptyField
     *
     * @test
     */
    public function createWebsiteEmptyFields($emptyField, $fieldType)
    {
        //Data
        if ($emptyField == 'terms_and_conditions') {
            $overrideData = array($emptyField => 'No');
        } else {
            $overrideData = array($emptyField => '');
        }
        $website = $this->loadData('create_website_empty_fields', $overrideData);
        //Steps
        $this->navigate('manage_staging_websites');
        $this->stagingWebsiteHelper()->createStagingWebsite($website);
        //Verification
        if ($emptyField != 'terms_and_conditions') {
            $this->addFieldIdToMessage($fieldType, $emptyField);
            $this->assertMessagePresent('validation', 'empty_required_field');
        } else {
            $this->assertMessagePresent('validation', 'website_item_must_be_checked');
        }
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Data provider for createWebsiteEmptyFields test</p>
     *
     * @return array
     */
    public function createWebsiteEmptyFieldsDataProvider()
    {
        return array(
            array('staging_website_code', 'field'),
            array('staging_website_name', 'field'),
            array('http_login', 'field'),
            array('http_password', 'field'),
            array('terms_and_conditions', '')
        );
    }

    /**
     * <p>Create Website with incorrect code</p>
     *
     * <p>Steps:</p>
     * <p>1. Open Content Staging > Staging Websites</p>
     * <p>2. Press button "Add Staging Website";</p>
     * <p>3. Select Source Website and press button "Continue";</p>
     * <p>4. Specify all settings/options for Staging Website, but enter "(test)" to website code field;</p>
     * <p>5. Press button "Create".</p>
     *
     * <p>Expected Results:</p>
     * <p>1. New Staging Website has not been created;</p>
     * <p>2. Message "Website code may only contain letters (a-z), numbers (0-9) or underscore(_), the first character must be a letter." appears;</p>
     *
     * @test
     */
    public function createWebsiteWithIncorrectCode()
    {
        //Data
        $website = $this->loadData('staging_website', array('staging_website_code' => '(test)'));
        //Steps
        $this->navigate('manage_staging_websites');
        $this->stagingWebsiteHelper()->createStagingWebsite($website);
        //Verification
        $this->assertMessagePresent('error', 'error_characters_in_code');
    }

    /**
     * <p>Test case TL-MAGE-2012: Editing/reconfiguring existing Staging Website</p>
     *
     * <p>Steps:</p>
     * <p>1. Go to system - Content Staging - Staging Websites;</p>
     * <p>2. Press button "Add Staging Website";</p>
     * <p>3. Select Source Website and press button "Continue";</p>
     * <p>4. Specify all settings/options for Staging Website:</p>
     * <p>Staging Website Code, Staging Website Name, Frontend Restriction</p>
     * <p>and Select Original Website Content to be Copied to the Staging Website</p>
     * <p>5. Press button "Create".</p>
     * <p>6. Edit newly created website by changing restriction;</p>
     *
     * <p>Expected Results:</p>
     * <p>1. New Staging Website has been created;</p>
     * <p>2. Admin user is redirected to Manage Staging Website page;</p>
     * <p>3. Message "The staging website has been saved." appears;</p>
     * <p>4. Website has been edited.</p>
     *
     * @test
     */
    public function editWebsite()
    {
        //Data
        $website = $this->loadData('staging_website');
        $searchData = array('filter_website_name' => $website['general_information']['staging_website_name']);
        $editWebsite = $this->loadData('edit_staging_website');
        //Steps
        $this->navigate('manage_staging_websites');
        $this->stagingWebsiteHelper()->createStagingWebsite($website);
        //Verification
        $this->assertMessagePresent('success', 'success_created_website');
        //Steps
        $this->addParameter('elementTitle', $website['general_information']['staging_website_name']);
        $this->stagingWebsiteHelper()->openStagingWebsite($searchData);
        $this->fillForm($editWebsite['general_information']);
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_website');
    }
}
