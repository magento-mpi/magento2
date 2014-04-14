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
 * Tax Rule creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Tax_TaxRule_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Save rule name for clean up</p>
     */
    protected $_ruleToBeDeleted = array();

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Sales->Tax->Manage Tax Rules</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_tax_rule');
    }

    /**
     * <p>Remove Tax rule after test</p>
     */
    protected function tearDownAfterTest()
    {
        if (!empty($this->_ruleToBeDeleted)) {
            $this->loginAdminUser();
            $this->navigate('manage_tax_rule');
            $this->taxHelper()->deleteTaxItem($this->_ruleToBeDeleted, 'rule');
            $this->_ruleToBeDeleted = array();
        }
    }

    /**
     * <p>Create Tax Rate for tests<p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $taxRateData = $this->loadDataSet('Tax', 'tax_rate_create_test');
        //Steps
        $this->navigate('manage_tax_zones_and_rates');
        $this->taxHelper()->createTaxRate($taxRateData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_rate');

        return $taxRateData;
    }

    /**
     * <p>Creating Tax Rule with required fields</p>
     *
     * @param array $testData
     * @return array $taxRuleData
     * @test
     * @depends preconditionsForTests
     */
    public function withRequiredFieldsOnly($testData)
    {
        //Data
        $taxRuleData = $this->loadDataSet('Tax', 'new_tax_rule_required',
            array('tax_rate' => $testData['tax_identifier']));
        $searchTaxRuleData = $this->loadDataSet('Tax', 'search_tax_rule', array('filter_name' => $taxRuleData['name']));
        //Steps
        $this->taxHelper()->createTaxRule($taxRuleData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_rule');
        //Steps
        $this->taxHelper()->openTaxItem($searchTaxRuleData, 'rule');
        $this->clickControl('link', 'tax_rule_info_additional_link');
        //Verifying
        $this->assertTrue($this->verifyForm($taxRuleData), $this->getParsedMessages());
        return $taxRuleData;
    }

    /**
     * <p>Creating Tax Rule with name that exists</p>
     *
     * @param array $taxRuleData
     *
     * @test
     * @depends withRequiredFieldsOnly
     */
    public function withNameThatAlreadyExists($taxRuleData)
    {
        //Data
        $searchTaxRuleData = $this->loadDataSet('Tax', 'search_tax_rule', array('filter_name' => $taxRuleData['name']));
        $this->_ruleToBeDeleted = $searchTaxRuleData;
        //Steps
        $this->taxHelper()->createTaxRule($taxRuleData);
        //Verifying
        $this->assertMessagePresent('error', 'code_already_exists');
    }

    /**
     * <p>Creating a Tax Rule with empty required fields.</p>
     *
     * @param string $emptyField Name of the field to leave empty
     * @param string $fieldType
     * @param array $testData
     *
     * @test
     * @dataProvider withEmptyRequiredFieldsDataProvider
     * @depends preconditionsForTests
     */
    public function withEmptyRequiredFields($emptyField, $fieldType, $testData)
    {
        //Data
        $taxRuleData = $this->loadDataSet('Tax', 'new_tax_rule_required',
            array('tax_rate' => $testData['tax_identifier'], $emptyField => ''));
        //Steps
        $this->taxHelper()->createTaxRule($taxRuleData);
        //Verifying
        $this->addFieldIdToMessage($fieldType, $emptyField);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * Test data for withEmptyRequiredFields
     *
     * @return array
     */
    public function withEmptyRequiredFieldsDataProvider()
    {
        return array(
            array('name', 'field'),
            array('customer_tax_class', 'composite_multiselect'),
            array('product_tax_class', 'composite_multiselect'),
            array('tax_rate', 'composite_multiselect'),
            array('priority', 'field'),
            array('sort_order', 'field')
        );
    }

    /**
     * Creating a new Tax Rule with invalid values for Name.
     * Fails because of MAGE-5237
     *
     * @param array $specialValue
     * @param array $testData
     *
     * @test
     * @dataProvider withSpecialValuesDataProvider
     * @depends preconditionsForTests
     */
    public function withSpecialValues($specialValue, $testData)
    {
        //Data
        $taxRuleData = $this->loadDataSet('Tax', 'new_tax_rule_required',
            array('tax_rate' => $testData['tax_identifier'], 'name' => $specialValue));
        $searchTaxRuleData = $this->loadDataSet('Tax', 'search_tax_rule', array('filter_name' => $taxRuleData['name']));
        //Steps
        $this->taxHelper()->createTaxRule($taxRuleData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_rule');
        $this->_ruleToBeDeleted = $searchTaxRuleData;
        //Steps
        $this->taxHelper()->openTaxItem($searchTaxRuleData, 'rule');
        $this->clickControl('link', 'tax_rule_info_additional_link');
        //Verifying
        $this->assertTrue($this->verifyForm($taxRuleData), $this->getParsedMessages());
    }

    /**
     * Test data for withSpecialValues
     *
     * @return array
     */
    public function withSpecialValuesDataProvider()
    {
        return array(
            array($this->generate('string', 255)),
            array($this->generate('string', 50, ':punct:'))
        );
    }

    /**
     * <p>Creating a new Tax Rule with invalid values for Priority.</p>
     *
     * @param string $specialValue
     * @param array $testData
     *
     * @test
     * @dataProvider invalidValuesDataProvider
     * @depends preconditionsForTests
     */
    public function withInvalidValuesForPriority($specialValue, $testData)
    {
        //Data
        $taxRuleData = $this->loadDataSet('Tax', 'new_tax_rule_required',
            array('tax_rate' => $testData['tax_identifier'], 'priority' => $specialValue));
        //Steps
        $this->taxHelper()->createTaxRule($taxRuleData);
        //Verifying
        $this->addFieldIdToMessage('field', 'priority');
        $this->assertMessagePresent('validation', 'enter_zero_or_greater');
    }

    /**
     * <p>Creating a new Tax Rule with invalid values for Sort Order.</p>
     *
     * @param string $specialValue
     * @param array $testData
     *
     * @test
     * @dataProvider invalidValuesDataProvider
     * @depends preconditionsForTests
     */
    public function withInvalidValuesForSortOrder($specialValue, $testData)
    {
        //Data
        $taxRuleData = $this->loadDataSet('Tax', 'new_tax_rule_required',
            array('tax_rate' => $testData['tax_identifier'], 'sort_order' => $specialValue));
        //Steps
        $this->taxHelper()->createTaxRule($taxRuleData);
        //Verifying
        $this->addFieldIdToMessage('field', 'sort_order');
        $this->assertMessagePresent('validation', 'enter_zero_or_greater');
    }

    /**
     * <p>Test data for withInvalidValuesForPriority, withInvalidValuesForSortOrder.</p>
     *
     * @return array
     */
    public function invalidValuesDataProvider()
    {
        return array(
            array($this->generate('string', 50, ':alpha:')),
            array($this->generate('string', 50, ':punct:'))
        );
    }
}