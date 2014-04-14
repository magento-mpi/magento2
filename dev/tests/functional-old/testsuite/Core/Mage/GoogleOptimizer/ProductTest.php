<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Core_Mage_GoogleOptimizer_ProductTest extends Mage_Selenium_TestCase
{
    /**
     * @var array
     */
    protected static $_productData;

    public function setUpBeforeTests()
    {
        $this->loginAdminUser();

        // Enable in System Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GoogleApi/content_experiments_enable');

        // Open manage products
        $this->navigate('manage_products');

        // Create product with experiment_code
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        $productData['google_experiment_code'] = 'experiment_code';
        $this->productHelper()->addTab('google_experiment')->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');

        self::$_productData = $productData;
    }

    public function tearDownAfterTestClass()
    {
        // Delete fixture
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->searchAndChoose(array('product_sku' => self::$_productData['general_sku']), 'product_grid');
        $this->addParameter('qtyDeletedProducts', 1);
        $this->fillDropdown('mass_action_select_action', 'Delete');
        $this->clickButtonAndConfirm('submit', 'confirmation_for_delete');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_products_massaction');
    }

    /**
     * @test
     * @group goinc
     */
    public function checkBehaviorOnCreate()
    {
        // Open product on frontend
        $this->frontend();
        $this->productHelper()->frontOpenProduct(self::$_productData['general_name']);

        // Check result
        $this->assertTrue(
            $this->textIsPresent(self::$_productData['google_experiment_code']),
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

        // Open manage products
        $this->navigate('manage_products');

        // Update experiment_code
        $this->productHelper()->openProduct(array('product_sku' => self::$_productData['general_sku']));
        self::$_productData['google_experiment_code'] = 'experiment_code_updated';
        $this->productHelper()
            ->fillProductInfo(array('google_experiment_code' => self::$_productData['google_experiment_code']));
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');

        // Open product on frontend
        $this->frontend();
        $this->productHelper()->frontOpenProduct(self::$_productData['general_name']);

        // Check result
        $this->assertTrue(
            $this->textIsPresent(self::$_productData['google_experiment_code']),
            'Experiment code is not equal.'
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
        $this->flushCache();

        // Open product on frontend
        $this->frontend();
        $this->productHelper()->frontOpenProduct(self::$_productData['general_name']);

        // Check result
        $this->assertFalse(
            $this->textIsPresent(self::$_productData['google_experiment_code']),
            'Experiment code is not disabled.'
        );
    }
}
