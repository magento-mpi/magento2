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
     * <p>Create Tax Rate for tests<p>
     * <p>Create Product Tax class for tests</p>
     *
     * @return array
     * @test
     * @group preConditions
     */
    public function preconditionsForTests()
    {
        //Data
        $taxRateData = $this->loadDataSet('Tax', 'tax_rate_create_test');
        $productTaxClassData = $this->loadDataSet('Tax', 'new_product_tax_class');
        //Steps
        $this->navigate('manage_tax_zones_and_rates');
        $this->taxHelper()->createTaxItem($taxRateData, 'rate');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_rate');
        //Steps
        $this->navigate('manage_product_tax_class');
        $this->taxHelper()->createTaxItem($productTaxClassData, 'product_class');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_class');

        return array('tax_rate'          => $taxRateData['tax_identifier'],
                     'product_tax_class' => $productTaxClassData['product_class_name']);
    }

    /**
     * <p>Creating Tax Rule with required fields</p>
     * <p>Steps</p>
     * <p>1. Click "Add New Tax Rule" button </p>
     * <p>2. Fill in required fields</p>
     * <p>3. Click "Save Rule" button</p>
     * <p>Expected Result:</p>
     * <p>Tax Rule created, success message appears</p>
     *
     * @param array $testData
     *
     * @return array $taxRuleData
     * @test
     * @depends preconditionsForTests
     */
    public function withRequiredFieldsOnly($testData)
    {
        //Data
        $taxRuleData = $this->loadDataSet('Tax', 'new_tax_rule_required', $testData);
        $searchTaxRuleData = $this->loadDataSet('Tax', 'search_tax_rule', array('filter_name' => $taxRuleData['name']));
        //Steps
        $this->taxHelper()->createTaxItem($taxRuleData, 'rule');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_rule');
        //Steps
        $this->taxHelper()->openTaxItem($searchTaxRuleData, 'rule');
        //Verifying
        $this->assertTrue($this->verifyForm($taxRuleData), $this->getParsedMessages());
        return $taxRuleData;
    }

    /**
     * <p>Creating Tax Rule with name that exists</p>
     * <p>Steps</p>
     * <p>1. Click "Add New Tax Rule" button </p>
     * <p>2. Fill in Name with value that exists</p>
     * <p>3. Click "Save Rule" button</p>
     * <p>Expected Result:</p>
     * <p>Tax Rule should not be created, error message appears</p>
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
        $this->taxHelper()->createTaxItem($taxRuleData, 'rule');
        //Verifying
        $this->assertMessagePresent('error', 'code_already_exists');
    }

    /**
     * <p>Creating a Tax Rule with empty required fields.</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Tax Rule"</p>
     * <p>2. Fill in the fields, but leave one required field empty;</p>
     * <p>3. Click button "Save Rule".</p>
     * <p>Expected result:</p>
     * <p>Received error message</p>
     *
     * @param string $emptyFieldName Name of the field to leave empty
     * @param string $fieldType Type of the field to leave empty
     * @param array $testData
     *
     * @test
     * @dataProvider withEmptyRequiredFieldsDataProvider
     * @depends preconditionsForTests
     * @group skip_due_to_bug
     */
    public function withEmptyRequiredFields($emptyFieldName, $fieldType, $testData)
    {
        //Data
        $taxRuleData = $this->loadDataSet('Tax', 'new_tax_rule_required', $testData);
        $taxRuleData = $this->overrideArrayData(array($emptyFieldName => ''), $taxRuleData, 'byFieldKey');
        //Steps
        $this->taxHelper()->createTaxItem($taxRuleData, 'rule');
        //Verifying
        $this->addFieldIdToMessage($fieldType, $emptyFieldName);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withEmptyRequiredFieldsDataProvider()
    {
        return array(
            array('name', 'field'),
            array('customer_tax_class', 'multiselect'),
            array('product_tax_class', 'multiselect'),
            array('tax_rate', 'multiselect'),
            array('priority', 'field'),
            array('sort_order', 'field')
        );
    }

    /**
     * Fails because of MAGE-5237
     * <p>Creating a new Tax Rule with special values (long, special chars).</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Tax Rule"</p>
     * <p>2. Fill in the fields</p>
     * <p>3. Click button "Save Rule"</p>
     * <p>4. Open the Tax Rule</p>
     * <p>Expected result:</p>
     * <p>All fields has the same values.</p>
     *
     * @param array $specialValue
     * @param array $testData
     *
     * @test
     * @dataProvider withSpecialValuesDataProvider
     * @depends preconditionsForTests
     * @group skip_due_to_bug
     */
    public function withSpecialValues($specialValue, $testData)
    {
        //Data
        $taxRuleData = $this->loadDataSet('Tax', 'new_tax_rule_required', $testData);
        $taxRuleData = $this->overrideArrayData(array('name'=> $specialValue), $taxRuleData, 'byFieldKey');
        $searchTaxRuleData = $this->loadDataSet('Tax', 'search_tax_rule', array('filter_name' => $taxRuleData['name']));
        //Steps
        $this->taxHelper()->createTaxItem($taxRuleData, 'rule');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_rule');
        $this->_ruleToBeDeleted = $searchTaxRuleData;
        //Steps
        $this->taxHelper()->openTaxItem($searchTaxRuleData, 'rule');
        //Verifying
        $this->assertTrue($this->verifyForm($taxRuleData), $this->getParsedMessages());
    }

    public function withSpecialValuesDataProvider()
    {
        return array(
            array($this->generate('string', 255)),
            array($this->generate('string', 50, ':punct:'))
        );
    }

    /**
     * <p>Creating a new Tax Rule with invalid values for Priority.</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Tax Rule"</p>
     * <p>2. Fill in the Priority field with invalid value</p>
     * <p>3. Click button "Save Rule"</p>
     * <p>Expected result:</p>
     * <p>Error message: Please enter a valid number in this field.</p>
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
        $taxRuleData = $this->loadDataSet('Tax', 'new_tax_rule_required', $testData);
        $taxRuleData = $this->overrideArrayData(array('priority'=> $specialValue), $taxRuleData, 'byFieldKey');
        //Steps
        $this->taxHelper()->createTaxItem($taxRuleData, 'rule');
        //Verifying
        $this->addFieldIdToMessage('field', 'priority');
        $this->assertMessagePresent('error', 'enter_not_negative_number');
    }

    /**
     * <p>Creating a new Tax Rule with invalid values for Sort Order.</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Tax Rule"</p>
     * <p>2. Fill in the Sort Order field with invalid value</p>
     * <p>3. Click button "Save Rule"</p>
     * <p>Expected result:</p>
     * <p>Error message: Please enter a valid number in this field.</p>
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
        $taxRuleData = $this->loadDataSet('Tax', 'new_tax_rule_required', $testData);
        $taxRuleData = $this->overrideArrayData(array('sort_order'=> $specialValue), $taxRuleData, 'byFieldKey');
        //Steps
        $this->taxHelper()->createTaxItem($taxRuleData, 'rule');
        //Verifying
        $this->addFieldIdToMessage('field', 'sort_order');
        $this->assertMessagePresent('error', 'enter_not_negative_number');
    }

    public function invalidValuesDataProvider()
    {
        return array(
            array($this->generate('string', 50, ':alpha:')),
            array($this->generate('string', 50, ':punct:'))
        );
    }
}