<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax Rule creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tax_TaxRule_CreateTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Save rule name for clean up</p>
     */
    protected $ruleToBeDeleted = null;

    /**
     * <p>Login to backend</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Sales->Tax->Manage Tax Rules</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_tax_rule');
    }


    /**
     * <p>Create Tax Rate for tests<p>
     *
     * @return array $taxRateData
     *
     * @test
     */
    public function setupTestDataCreateTaxRate()
    {
        //Data
        $taxRateData = $this->loadData('tax_rate_create_test', null, 'tax_identifier');
        //Steps
        $this->navigate('manage_tax_zones_and_rates');
        $this->taxHelper()->createTaxRate($taxRateData);
        $this->assertMessagePresent('success', 'success_saved_tax_rate');
        return $taxRateData;
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
     * @depends setupTestDataCreateTaxRate
     * @param array $taxRateData
     * @return array $taxRuleData
     * @test
     */
    public function withRequiredFieldsOnly($taxRateData)
    {
        //Data
        $taxRuleData = $this->loadData('new_tax_rule_required',
                                       array('tax_rate'=>$taxRateData['tax_identifier']),'name');
        $searchTaxRuleData = $this->loadData('search_tax_rule',
                                             array('filter_name' => $taxRuleData['name']));
        //Steps
        $this->taxHelper()->createTaxItem($taxRuleData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_rule');
        $this->taxHelper()->openTaxItem($searchTaxRuleData ,'tax_rules');
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
     * @depends withRequiredFieldsOnly
     * @param array $taxRuleData
     * @test
     */
    public function withNameThatAlreadyExists($taxRuleData)
    {
        //Data
        $searchTaxRuleData = $this->loadData('search_tax_rule',
                                             array('filter_name' => $taxRuleData['name']));
        $this->ruleToBeDeleted = $searchTaxRuleData;
        //Steps
        $this->taxHelper()->createTaxItem($taxRuleData);
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
     * @dataProvider dataEmptyRequiredFields
     * @param string $emptyFieldName Name of the field to leave empty
     * @param string $fieldType Type of the field to leave empty
     * @param string $validationMessage Validation message to be verified
     *
     * @test
     */
    public function withEmptyRequiredFields($emptyFieldName,$fieldType,$validationMessage)
    {
        //Data
        $taxRateData = $this->loadData('new_tax_rule_required', array($emptyFieldName => ''),'name');
        //Steps
        $this->taxHelper()->createTaxItem($taxRateData);
        //Verifying
        $this->addFieldIdToMessage($fieldType, $emptyFieldName);
        $this->assertMessagePresent('error', $validationMessage);
    }

    public function dataEmptyRequiredFields()
    {
        return array(
            array('name','field','empty_required_field'),
            array('customer_tax_class','multiselect','empty_required_field'),
            array('product_tax_class','multiselect','empty_required_field'),
            array('tax_rate','multiselect','empty_required_field'),
            array('priority','field','enter_not_negative_number'),
            array('sort_order','field','enter_not_negative_number')
        );
    }

    /**
     * <p>Creating a new Tax Rule with special values (long, special chars).</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Tax Rule"</p>
     * <p>2. Fill in the fields</p>
     * <p>3. Click button "Save Rule"</p>
     * <p>4. Open the Tax Rule</p>
     * <p>Expected result:</p>
     * <p>All fields has the same values.</p>
     *
     * @depends setupTestDataCreateTaxRate
     * @dataProvider dataSpecialValues
     * @param array $taxRateData
     * @param array $specialValue
     *
     * @test
     */
    public function withSpecialValues($specialValue,$taxRateData)
    {
        //Data
        $taxRuleData = $this->loadData('new_tax_rule_required',
                                       array('tax_rate' => $taxRateData['tax_identifier'],
                                            'name' => $specialValue));
        $searchTaxRuleData = $this->loadData('search_tax_rule',
                                             array('filter_name' => $taxRuleData['name']));
        //Steps
        $this->taxHelper()->createTaxItem($taxRuleData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_rule');
        $this->ruleToBeDeleted = $searchTaxRuleData;
        $this->taxHelper()->openTaxItem($searchTaxRuleData ,'tax_rules');
        $this->assertTrue($this->verifyForm($taxRuleData), $this->getParsedMessages());
    }

    public function dataSpecialValues()
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
     * @depends setupTestDataCreateTaxRate
     * @dataProvider dataSpecialValuesFields
     * @test
     *
     * @param array $taxRateData
     * @param array $specialValue
     */
    public function withInvalidValuesForPriority($specialValue,$taxRateData)
    {
        //Data
        $taxRuleData = $this->loadData('new_tax_rule_required',
                                       array('tax_rate' => $taxRateData['tax_identifier'],
                                            'priority' => $specialValue),'name');
        //Steps
        $this->taxHelper()->createTaxItem($taxRuleData);
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
     * @depends setupTestDataCreateTaxRate
     * @dataProvider dataSpecialValuesFields
     * @test
     *
     * @param array $taxRateData
     * @param array $specialValue
     */
    public function withInvalidValuesForSortOrder($specialValue,$taxRateData)
    {
        //Data
        $taxRuleData = $this->loadData('new_tax_rule_required',
                                       array('tax_rate' => $taxRateData['tax_identifier'],
                                            'sort_order' => $specialValue),'name');
        //Steps
        $this->taxHelper()->createTaxItem($taxRuleData);
        //Verifying
        $this->addFieldIdToMessage('field', 'sort_order');
        $this->assertMessagePresent('error', 'enter_not_negative_number');
    }

    public function dataSpecialValuesFields()
    {
        return array(
            array($this->generate('string', 50,':alpha:')),
            array($this->generate('string', 50, ':punct:'))
        );
    }

    /**
     * Clean up
     */
    protected function tearDown()
    {
        //Remove Tax rule after test
        if (!is_null($this->ruleToBeDeleted)) {
            $this->navigate('manage_tax_rule');
            $this->taxHelper()->deleteTaxItem($this->ruleToBeDeleted ,'tax_rules');
            $this->ruleToBeDeleted = null;
        }
    }

}