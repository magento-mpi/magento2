<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Core_Mage_GoogleOptimizer_CategoryTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        parent::setUpBeforeTests();

        $this->loginAdminUser();
//        $this->navigate('system_configuration');
//        $this->systemConfigurationHelper()->configure('GoogleApi/content_experiments_enable');
    }

    public function tearDownAfterTest()
    {
        parent::tearDownAfterTest();
    }

    /**
     * @test
     * @group goinc
     */
    public function checkBehaviorOnCreate()
    {
        // Open manage categories
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();

        // Set experiment_code
        $categoryData = $this->loadDataSet('Category', 'sub_category_required');
        $categoryData['experiment_code'] = 'experiment_code';
        $this->categoryHelper()->createCategory($categoryData);
        $this->categoryHelper()->frontOpenCategory("{$categoryData['parent_category']}/{$categoryData['name']}");

        // Check result
        //$this->assertXpath( '//input[@id="LASTNAME"][@value=""]');
    }

    /**
     * @_test
     * @group goinc
     */
    public function checkBehaviorOnUpdate()
    {
    }

    /**
     * @_test
     * @group goinc
     */
    public function checkBehaviorIfDisabled()
    {
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GoogleApi/content_experiments_disable');
    }
}
