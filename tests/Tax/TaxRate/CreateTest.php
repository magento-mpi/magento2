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
 * Tax Rate creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tax_TaxRate_CreateTest extends Mage_Selenium_TestCase
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
     * <p>Navigate to Sales->Tax->Manage Tax Zones&Rates</p>
     */
    protected function assertPreConditions()
    {
        // @TODO
    }

    /**
     * <p>Creating Tax Rate with required fields</p>
     * <p>Steps</p>
     * <p>1. Click "Add New Tax Rate" button </p>
     * <p>2. Fill in required fields</p>
     * <p>3. Click "Save Rate" button</p>
     * <p>Expected Result:</p>
     * <p>Tax Rate created, success message appears</p>
     *
     * @dataProvider dataTaxRateRequired
     * @test
     *
     * @param array $taxRateData
     */
    public function withRequiredFieldsOnly($taxRateData)
    {
        $this->markTestIncomplete('@TODO');
    }

    public function dataTaxRateRequired()
    {
        return array(
            array(),// Zip/Post is Range => No
            array() // Zip/Post is Range => Yes
        );
    }

    /**
     * <p>Creating Tax Rate with name that exists</p>
     * <p>Steps</p>
     * <p>1. Click "Add New Tax Rate" button </p>
     * <p>2. Fill in Tax Identifier with value that exists</p>
     * <p>3. Click "Save Rate" button</p>
     * <p>Expected Result:</p>
     * <p>Tax Rate should not be created, error message appears</p>
     *
     * @test
     */
    public function withTaxIdentifierThatAlreadyExists()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Creating a Tax Rate with empty required fields.</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Tax Rate"</p>
     * <p>2. Fill in the fields, but leave one required field empty;</p>
     * <p>3. Click button "Save Rate".</p>
     * <p>Expected result:</p>
     * <p>Received error message "This is a required field."</p>
     *
     * @dataProvider dataEmptyRequiredFields
     * @test
     *
     * @param string $taxRateData Name of the field to leave empty
     * @param string $validationMessage Validation message to be verified
     */
    public function withEmptyRequiredFields($taxRateData,$validationMessage)
    {
        $this->markTestIncomplete('@TODO');
    }

    public function dataEmptyRequiredFields()
    {
        return array(
            array(),//EmptyTaxIdentifier
            array(),//EmptyRatePercent
            array(),//EmptyRangeFrom
            array()//EmptyRangeTo
        );
    }

    /**
     * <p>Creating a new Tax Rate with special values (long, special chars).</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Tax Rate"</p>
     * <p>2. Fill in the fields</p>
     * <p>3. Click button "Save Rate"</p>
     * <p>4. Open the Tax Rate</p>
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
     * <p>Creating a new Tax Rate with invalid values for Range From\To.</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Tax Rate"</p>
     * <p>2. Fill in the fields Range From\To with invalid value</p>
     * <p>3. Click button "Save Rate"</p>
     * <p>Expected result:</p>
     * <p>Please use numbers only in this field. Please avoid spaces or other characters such as dots or commas.</p>
     *
     * @dataProvider dataSpecialValuesRange
     * @test
     *
     * @param array $specialValue
     */
    public function withInvalidValuesForRange($specialValue)
    {
        // @TODO
    }

    public function dataSpecialValuesRange()
    {
        return array(
            array(),// string
            array(),// Number with space
            array() // special chars
        );
    }

    /**
     * <p>Creating a new Tax Rate with invalid values for Rate Percent.</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Tax Rate"</p>
     * <p>2. Fill in the field Rate Percent with invalid value</p>
     * <p>3. Click button "Save Rate"</p>
     * <p>Expected result:</p>
     * <p>Error message: Please enter a valid number in this field.</p>
     *
     * @dataProvider dataSpecialValuesRatePercent
     * @test
     *
     * @param array $specialValue
     */
    public function withInvalidValueForRatePercent($specialValue)
    {
        // @TODO
    }


    public function dataSpecialValuesRatePercent()
    {
        return array(
            array(),// string
            array() // special chars
        );
    }


    /**
     * <p>Creating a new Tax Rate with State.</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Tax Rate"</p>
     * <p>2. Fill in the fields, select value for State</p>
     * <p>3. Click button "Save Rate"</p>
     * <p>4. Open the Tax Rate</p>
     * <p>Expected result:</p>
     * <p>All fields has the same values.</p>
     *
     * @test
     *
     */
    public function test_WithSelectedState()
    {
        // @TODO
    }

}
