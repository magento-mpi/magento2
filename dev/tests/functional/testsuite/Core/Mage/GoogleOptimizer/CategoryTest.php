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
    protected static $_categoryData;

    public function setUpBeforeTests()
    {
        $this->loginAdminUser();

        // Enable in System Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GoogleApi/content_experiments_enable');

        // Open manage categories
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();

        // Create category with experiment_code
        $categoryData = $this->loadDataSet('Category', 'sub_category_required');
        $categoryData['experiment_code'] = 'experiment_code';
        $this->categoryHelper()->createCategory($categoryData);

        self::$_categoryData = $categoryData;
    }

    public function tearDownAfterTestClass()
    {
        $this->loginAdminUser();

        // Delete fixture
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->selectCategory(
            sprintf('%s/%s', self::$_categoryData['parent_category'], self::$_categoryData['name'])
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
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory(self::$_categoryData['name']);

        // Check result
        $this->assertTrue($this->textIsPresent(self::$_categoryData['experiment_code']),
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
            sprintf('%s/%s', self::$_categoryData['parent_category'], self::$_categoryData['name'])
        );
        self::$_categoryData['experiment_code'] = 'experiment_code_updated';
        $this->categoryHelper()->fillCategoryInfo(array('experiment_code' => self::$_categoryData['experiment_code']));
        $this->clickButton('save_category');

        // Open category on frontend
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory(self::$_categoryData['name']);

        // Check result
        $this->assertTrue($this->textIsPresent(self::$_categoryData['experiment_code']),
            'Experiment code is not equal.');
    }

    /**
     * @test
     * @group goinc
     */
    public function checkBehaviorOnEmptyUpdate()
    {
        $this->loginAdminUser();

        // Open manage categories
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();

        // Update experiment_code
        $this->categoryHelper()->selectCategory(
            sprintf('%s/%s', self::$_categoryData['parent_category'], self::$_categoryData['name'])
        );
        $this->categoryHelper()->fillCategoryInfo(array('experiment_code' => ''));
        $this->clickButton('save_category');

        // Open category on frontend
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory(self::$_categoryData['name']);
        // Check result
        $this->assertFalse($this->textIsPresent(self::$_categoryData['experiment_code']),
            'Experiment code is present.');
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
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory(self::$_categoryData['name']);

        // Check result
        $this->assertFalse($this->textIsPresent(self::$_categoryData['experiment_code']),
            'Experiment code is not disabled.');
    }
}
