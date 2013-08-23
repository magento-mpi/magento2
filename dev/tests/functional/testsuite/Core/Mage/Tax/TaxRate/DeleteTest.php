<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Tax
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tax Rate deletion tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Tax_TaxRate_DeleteTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Save rule name for clean up</p>
     */
    protected $_ruleToBeDeleted = array();

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Sales->Tax->Manage Tax Zones&Rates</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_tax_zones_and_rates');
    }

    protected function tearDownAfterTest()
    {
        //Remove Tax rule after test
        if (!empty($this->_ruleToBeDeleted)) {
            $this->loginAdminUser();
            $this->navigate('manage_tax_rule');
            $this->taxHelper()->deleteTaxItem($this->_ruleToBeDeleted, 'rule');
            $this->_ruleToBeDeleted = array();
        }
    }

    /**
     * <p>Delete a Tax Rate</p>
     *
     * @test
     */
    public function notUsedInRule()
    {
        //Data
        $taxRate = $this->loadDataSet('Tax', 'tax_rate_create_test_zip_no');
        $search = $this->loadDataSet('Tax', 'search_tax_rate', array('filter_tax_id' => $taxRate['tax_identifier']));
        //Steps
        $this->taxHelper()->createTaxRate($taxRate);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_rate');
        //Steps
        $this->taxHelper()->deleteTaxItem($search, 'rate');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_tax_rate');
    }

    /**
     * <p>Delete a Tax Rate that used</p>
     *
     * @test
     */
    public function usedInRule()
    {
        //Data
        $rate = $this->loadDataSet('Tax', 'tax_rate_create_test_zip_no');
        $searchRate = $this->loadDataSet('Tax', 'search_tax_rate', array('filter_tax_id' => $rate['tax_identifier']));
        $rule = $this->loadDataSet('Tax', 'new_tax_rule_required', array('tax_rate' => $rate['tax_identifier']));
        $searchRule = $this->loadDataSet('Tax', 'search_tax_rule', array('filter_name' => $rule['name']));
        //Steps
        $this->taxHelper()->createTaxRate($rate);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_rate');
        //Steps
        $this->navigate('manage_tax_rule');
        $this->taxHelper()->createTaxRule($rule);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_rule');
        $this->_ruleToBeDeleted = $searchRule;
        //Steps
        $this->navigate('manage_tax_zones_and_rates');
        $this->taxHelper()->deleteTaxItem($searchRate, 'rate');
        //Verifying
        $this->assertMessagePresent('error', 'error_delete_tax_rate');
    }
}