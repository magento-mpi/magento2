<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Core_Mage_GoogleOptimizer_CategoryTest extends Mage_Selenium_TestCase
{
    /**
     * @var array
     */
    protected $_categoryData;

    public function assertPreConditions()
    {
        parent::assertPreConditions();

        if (!$this->_categoryData) {
            $this->loginAdminUser();

            // Enable in System Configuration
            $this->navigate('system_configuration');
            $this->systemConfigurationHelper()->configure('GoogleApi/content_experiments_enable');

            // Open manage categories
            $this->navigate('manage_categories', false);
            $this->categoryHelper()->checkCategoriesPage();

            // Set experiment_code
            $categoryData = $this->loadDataSet('Category', 'sub_category_required');
            $categoryData['experiment_code'] = 'experiment_code';
            $this->categoryHelper()->createCategory($categoryData);
            $this->_categoryData = $categoryData;
        }
    }

    public function tearDownAfterTest()
    {
        parent::tearDownAfterTest();

        // Delete fixture
        $this->loginAdminUser();
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->selectCategory(
            sprintf('%s/%s', $this->_categoryData['parent_category'], $this->_categoryData['name'])
        );
        $this->categoryHelper()->deleteCategory('delete_category', 'confirm_delete');
        $this->assertMessagePresent('success', 'success_deleted_category');
    }

    /**
     * @test
     * @group goinc
     */
    public function checkBehaviorOnCreate()
    {
        // Open category on frontend
        $this->frontend('home');
        $this->categoryHelper()->frontOpenCategory($this->_categoryData['name']);

        // Check result
        $this->assertTrue($this->textIsPresent($this->_categoryData['experiment_code']),
            'Experiment code is not found.');
    }

    /**
     * @test
     * @group goinc
     */
    public function checkBehaviorOnUpdate()
    {
        $this->loginAdminUser();

        // Open manage categories
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();

        // Update experiment_code
        $this->categoryHelper()->selectCategory(
            sprintf('%s/%s', $this->_categoryData['parent_category'], $this->_categoryData['name'])
        );
        $this->_categoryData['experiment_code'] = 'experiment_code_updated';
        $this->categoryHelper()->fillCategoryInfo(array('experiment_code' => $this->_categoryData['experiment_code']));
        $this->clickButton('save_category');

        // Open category on frontend
        $this->frontend('home');
        $this->categoryHelper()->frontOpenCategory($this->_categoryData['name']);

        // Check result
        $this->assertTrue($this->textIsPresent($this->_categoryData['experiment_code']),
            'Experiment code is not found.');
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

        // Open category on frontend
        $this->frontend('home');
        $this->categoryHelper()->frontOpenCategory($this->_categoryData['name']);

        // Check result
        $this->assertFalse($this->textIsPresent($this->_categoryData['experiment_code']),
            'Experiment code is not disabled.');
    }
}
