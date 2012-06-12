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
 * Tax Rule deletion tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Tax_TaxRule_DeleteTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Sales->Tax->Manage Tax Zones&Rates</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_tax_rule');
    }

    /**
     * <p>Create Tax Rate for tests<p>
     * @return array $taxRateData
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $taxRateData = $this->loadDataSet('Tax', 'tax_rate_create_test');
        //Steps
        $this->navigate('manage_tax_zones_and_rates');
        $this->taxHelper()->createTaxItem($taxRateData, 'rate');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_rate');
        return $taxRateData;
    }

    /**
     * <p>Delete a Tax Rule</p>
     * <p>Steps:</p>
     * <p>1. Create a new Tax Rule</p>
     * <p>2. Open the Tax Rule</p>
     * <p>3. Delete the Tax Rule</p>
     * <p>Expected result:</p>
     * <p>Received the message that the Tax Rule has been deleted.</p>
     *
     * @param array $taxRateData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function deleteTaxRule($taxRateData)
    {
        //Data
        $rule = $this->loadDataSet('Tax', 'new_tax_rule_required', array('tax_rate' => $taxRateData['tax_identifier']));
        $searchTaxRule = $this->loadDataSet('Tax', 'search_tax_rule', array('filter_name' => $rule['name']));
        //Steps
        $this->taxHelper()->createTaxItem($rule, 'rule');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_rule');
        //Steps
        $this->taxHelper()->deleteTaxItem($searchTaxRule, 'rule');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_tax_rule');
    }
}