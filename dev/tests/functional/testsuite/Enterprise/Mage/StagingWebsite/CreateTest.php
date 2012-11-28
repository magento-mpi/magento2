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
 * Create Website tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_StagingWebsite_CreateTest extends Mage_Selenium_TestCase
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

    protected function tearDownAfterTest()
    {
        //load default application settings
        $this->getConfigHelper()->getConfigAreas(true);
    }

    /**
     * <p>Test case TL-MAGE-2011 and TL-MAGE-2024:</p>
     * <p>Staging Website Creation</p>
     *
     * @return string $websiteCode
     * @test
     */
    public function createWebsite()
    {
        //Data
        $website = $this->loadDataSet('StagingWebsite', 'staging_website');
        $newFrontendUrl =
            $this->stagingWebsiteHelper()->buildFrontendUrl($website['general_information']['staging_website_code']);
        $creationStarted = $this->loadDataSet('Backups', 'staging_website_creation_started_log',
            array('filter_website_from' => 'Main Website'));
        $creationCompleted = $this->loadDataSet('Backups', 'staging_website_creation_completed_log',
            array('filter_website_from' => 'Main Website',
                  'filter_website_to'   => $website['general_information']['staging_website_name']));
        //Steps
        $this->navigate('manage_staging_websites');
        $this->stagingWebsiteHelper()->createStagingWebsite($website);
        //Verification
        $this->assertMessagePresent('success', 'success_created_website');
        $this->addParameter('websiteName', $website['general_information']['staging_website_name']);
        $this->addParameter('latestEvent', 'Staging Website Creation');
        $this->search(array('filter_website_name' => $website['general_information']['staging_website_name']),
            'staging_websites_grid');
        //search is used for getting on the page with the matched row
        $this->assertTrue($this->controlIsPresent('pageelement', 'latest_event'));
        //Verification on log page
        $this->navigate('manage_staging_operations_log');
        $this->searchAndOpen($creationStarted, 'staging_operations_log_grid');
        $this->navigate('manage_staging_operations_log');
        $this->searchAndOpen($creationCompleted, 'staging_operations_log_grid');
        $this->getConfigHelper()->setAreaBaseUrl('frontend', $newFrontendUrl);
        $this->frontend();
        return $website['general_information']['staging_website_code'];
    }

    /**
     * <p>Test case TL-MAGE-2019</p>
     * <p>Staging website creation - negative</p>
     *
     * @param $websiteCode
     *
     * @test
     * @depends createWebsite
     */
    public function createWebsiteWithExistingCode($websiteCode)
    {
        //Data
        $website =
            $this->loadDataSet('StagingWebsite', 'staging_website', array('staging_website_code' => $websiteCode));
        //Steps
        $this->navigate('manage_staging_websites');
        $this->stagingWebsiteHelper()->createStagingWebsite($website);
        //Verification
        $this->assertMessagePresent('error', 'error_website_with_the_same_code');
    }

    /**
     * <p>Create Website with empty fields</p>
     *
     * @param string $fieldType
     * @param string $emptyField
     *
     * @test
     * @dataProvider createWebsiteEmptyFieldsDataProvider
     */
    public function createWebsiteEmptyFields($emptyField, $fieldType)
    {
        //Data
        if ($emptyField == 'terms_and_conditions') {
            $overrideData = array($emptyField => 'No');
        } else {
            $overrideData = array($emptyField => '');
        }
        $website = $this->loadDataSet('StagingWebsite', 'create_website_empty_fields', $overrideData);
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
     * @test
     */
    public function createWebsiteWithIncorrectCode()
    {
        //Data
        $website = $this->loadDataSet('StagingWebsite', 'staging_website', array('staging_website_code' => '(test)'));
        //Steps
        $this->navigate('manage_staging_websites');
        $this->stagingWebsiteHelper()->createStagingWebsite($website);
        //Verification
        $this->assertMessagePresent('error', 'error_characters_in_code');
    }

    /**
     * <p>Test case TL-MAGE-2012: Editing/reconfiguring existing Staging Website</p>
     *
     * @test
     */
    public function editWebsite()
    {
        //Data
        $website = $this->loadDataSet('StagingWebsite', 'staging_website');
        $searchData = array('filter_website_name' => $website['general_information']['staging_website_name']);
        $editWebsite = $this->loadDataSet('StagingWebsite', 'edit_staging_website');
        //Steps
        $this->navigate('manage_staging_websites');
        $this->stagingWebsiteHelper()->createStagingWebsite($website);
        //Verification
        $this->assertMessagePresent('success', 'success_created_website');
        //Steps
        $this->addParameter('elementTitle', $website['general_information']['staging_website_name']);
        $this->stagingWebsiteHelper()->openStagingWebsite($searchData);
        $this->fillTab($editWebsite['general_information'], 'general_information');
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_website');
    }
}
