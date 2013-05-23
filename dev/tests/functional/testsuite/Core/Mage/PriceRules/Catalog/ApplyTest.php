<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_PriceRules
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Price Rules applying in frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_PriceRules_Catalog_ApplyTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Tax/default_tax_config');
        $this->systemConfigurationHelper()->configure('ShippingSettings/shipping_settings_default');
        $this->systemConfigurationHelper()->configure('Currency/enable_usd');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/disable_single_store_mode');
        $this->navigate('manage_catalog_price_rules');
        $this->priceRulesHelper()->deleteAllRules();
        $this->clickButton('apply_rules', false);
        $this->waitForNewPage();
        $this->assertMessagePresent('success', 'success_applied_rule');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->logoutCustomer();
        $this->loginAdminUser();
        $this->navigate('manage_catalog_price_rules');
        $this->priceRulesHelper()->deleteAllRules();
        $this->clickButton('apply_rules', false);
        $this->waitForNewPage();
        $this->assertMessagePresent('success', 'success_applied_rule');
    }

    /**
     * <p>Preconditions</p>
     * <p>Create Customer for tests</p>
     * <p>Creates Category to use during tests</p>
     * @return array
     * @test
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        //Steps
        $data = $this->productHelper()->createSimpleProduct(true);
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');
        $this->logoutCustomer();
        return array(
            'customer' => array('email' => $userData['email'], 'password' => $userData['password']),
            'categoryPath' => $data['category']['path'],
            'categoryName' => $data['category']['name'],
            'simpleName' => $data['simple']['product_name']
        );
    }

    /**
     * <p>Create catalog price rule - To Fixed Amount</p>
     *
     * @param string $ruleType
     * @param array $testData
     *
     * @test
     * @dataProvider applyRuleToSimpleFrontDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3308
     */
    public function applyRuleToSimpleFront($ruleType, $testData)
    {
        //Data
        $action = $this->loadDataSet('CatalogPriceRule', $ruleType);
        $condition = $this->loadDataSet('CatalogPriceRule', 'condition',
            array('category' => $testData['categoryPath']));
        $priceRule = $this->loadDataSet('CatalogPriceRule', 'test_catalog_rule',
            array('conditions' => $condition, 'status' => 'Active', 'actions' => $action));
        $override = array('product_name' => $testData['simpleName'], 'category' => $testData['categoryName']);
        $productPriceLogged = $this->loadDataSet('PriceReview', $ruleType . '_simple_product_logged');
        $prodPriceNotLogged = $this->loadDataSet('PriceReview', $ruleType . '_simple_product_not_logged');
        $inCategoryLogged = $this->loadDataSet('PriceReview', $ruleType . '_simple_logged_category', $override);
        $inCategoryNotLogged = $this->loadDataSet('PriceReview', $ruleType . '_simple_not_logged_category', $override);
        //Steps
        $this->navigate('manage_catalog_price_rules');
        $this->priceRulesHelper()->createRule($priceRule);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_rule');
        //Steps
        $this->clickButton('apply_rules', false);
        $this->waitForNewPage();
        $this->assertMessagePresent('success', 'success_applied_rule');
        $this->flushCache();
        $this->reindexInvalidedData();
        //Verification on frontend
        $this->frontend();
        $this->categoryHelper()->frontOpenCategoryAndValidateProduct($inCategoryNotLogged);
        $this->productHelper()->frontOpenProduct($testData['simpleName']);
        $this->categoryHelper()->frontVerifyProductPrices($prodPriceNotLogged);
        $this->customerHelper()->frontLoginCustomer($testData['customer']);
        $this->categoryHelper()->frontOpenCategoryAndValidateProduct($inCategoryLogged);
        $this->productHelper()->frontOpenProduct($testData['simpleName']);
        $this->categoryHelper()->frontVerifyProductPrices($productPriceLogged);
    }

    public function applyRuleToSimpleFrontDataProvider()
    {
        return array(
            array('by_percentage_of_the_original_price'),
            array('by_fixed_amount'),
            array('to_percentage_of_the_original_price'),
            array('to_fixed_amount')
        );
    }
}