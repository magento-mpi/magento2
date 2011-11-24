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
        // @TODO
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
     * @test
     */
    public function withRequiredFieldsOnly()
    {
        $this->markTestIncomplete('@TODO');
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
     * @test
     *
     * @param string $taxRuleData Name of the field to leave empty
     * @param string $validationMessage Validation message to be verified
     */
    public function withEmptyRequiredFields($taxRateData,$validationMessage)
    {
        $this->markTestIncomplete('@TODO');
    }

    public function dataEmptyRequiredFields()
    {
        return array(
            array(),//Name
            array(),//Customer Tax Class
            array(),//Product Tax Class
            array(),//Tax Rate
            array(),//Priority
            array()//Sort Order
        );
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
     * @test
     */
    public function withNameThatAlreadyExists()
    {
        $this->markTestIncomplete('@TODO');
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
     * @dataProvider dataSpecialValues
     * @test
     *
     * @param array $specialValue
     */
    public function withSpecialValues($specialValue)
    {
        // @TODO
    }

    public function dataSpecialValues()
    {
        return array(
            array(array()),//$this->generate('string', 255)
            array(array()) //$this->generate('string', 50, ':punct:')
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
     * @dataProvider dataSpecialValuesFields
     * @test
     *
     * @param array $specialValue
     */
    public function withInvalidValuesForPriority($specialValue)
    {
        // @TODO
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
     * @dataProvider dataSpecialValuesFields
     * @test
     *
     * @param array $specialValue
     */
    public function withInvalidValuesForSortOrder($specialValue)
    {
        // @TODO
    }

    public function dataSpecialValuesFields()
    {
        return array(
            array(),// string
            array() // special chars
        );
    }

}
