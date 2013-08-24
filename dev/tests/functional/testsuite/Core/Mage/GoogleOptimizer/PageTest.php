<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Core_Mage_GoogleOptimizer_PageTest extends Mage_Selenium_TestCase
{
    /**
     * @var array
     */
    protected static $_pageData;

    public function setUpBeforeTests()
    {
        $this->loginAdminUser();

        // Enable in System Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GoogleApi/content_experiments_enable');

        // Open manage pages
        $this->navigate('manage_cms_pages');

        // Create page with experiment_code
        $pageData = $this->loadDataSet('CmsPage', 'new_cms_page_req');
        $pageData['additional_tabs']['google_experiment'] = array('experiment_code' => 'experiment_code');
        unset($pageData['content']['widgets']);
        $this->cmsPagesHelper()->createCmsPage($pageData);
        $this->assertMessagePresent('success', 'success_saved_cms_page');

        self::$_pageData = $pageData;
    }

    public function tearDownAfterTestClass()
    {
        // Delete fixture
        $this->loginAdminUser();
        $this->navigate('manage_cms_pages');
        $search = array(
            'filter_title' => self::$_pageData['page_information']['page_title'],
            'filter_url_key' => self::$_pageData['page_information']['url_key'],
        );
        $this->cmsPagesHelper()->deleteCmsPage($search);
        $this->assertMessagePresent('success', 'success_deleted_cms_page');
    }

    /**
     * @test
     * @group goinc
     */
    public function checkBehaviorOnCreate()
    {
        // Open page on frontend
        $this->frontend();
        $this->cmsPagesHelper()->frontOpenCmsPage(self::$_pageData);

        // Check result
        $this->assertTrue(
            $this->textIsPresent(self::$_pageData['additional_tabs']['google_experiment']['experiment_code']),
            'Experiment code is not found.'
        );
    }

    /**
     * @test
     * @group goinc
     */
    public function checkBehaviorOnUpdate()
    {
        $this->loginAdminUser();

        // Open manage pages
        $this->navigate('manage_cms_pages');

        // Update experiment_code
        $this->cmsPagesHelper()->openCmsPage(self::$_pageData);
        self::$_pageData['additional_tabs']['google_experiment']['experiment_code'] = 'experiment_code_updated';
        $this->fillTab(self::$_pageData['additional_tabs']['google_experiment'], 'google_experiment');
        $this->saveForm('save_page');

        // Open page on frontend
        $this->frontend();
        $this->cmsPagesHelper()->frontOpenCmsPage(self::$_pageData);

        // Check result
        $this->assertTrue(
            $this->textIsPresent(self::$_pageData['additional_tabs']['google_experiment']['experiment_code']),
            'Experiment code is not found.'
        );
    }

    /**
     * @test
     * @group goinc
     */
    public function checkBehaviorIfDisabled()
    {
        $this->loginAdminUser();

        // Disable in System Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GoogleApi/content_experiments_disable');

        // Open page on frontend
        $this->frontend();
        $this->cmsPagesHelper()->frontOpenCmsPage(self::$_pageData);

        // Check result
        $this->assertFalse(
            $this->textIsPresent(self::$_pageData['additional_tabs']['google_experiment']['experiment_code']),
            'Experiment code is not disabled.'
        );
    }
}
