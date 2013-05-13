<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ProductAttribute
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Create new product attribute. Type: Multiple Select
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_ProductAttribute_Create_MultiSelectTest extends Mage_Selenium_TestCase
{
    /**
     * Preconditions:
     * Navigate to System -> Manage Attributes.
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_attributes');
    }

    /**
     * @test
     */
    public function navigation()
    {
        $this->assertTrue($this->buttonIsPresent('add_new_attribute'),
            'There is no "Add New Attribute" button on the page');
        $this->clickButton('add_new_attribute');
        $this->assertTrue($this->checkCurrentPage('new_product_attribute'), $this->getParsedMessages());
        $this->assertTrue($this->buttonIsPresent('back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_attribute'), 'There is no "Save" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_and_continue_edit'),
            'There is no "Save and Continue Edit" button on the page');
    }

    /**
     * Create "Multiple Select" type Product Attribute (required fields only)
     *
     * @return array
     * @test
     * @depends navigation
     * @TestlinkId TL-MAGE-3557
     */
    public function withRequiredFieldsOnly()
    {
        //Data
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_multiselect');
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');

        return $attrData;
    }

    /**
     * Checking of verification for duplicate of Product Attributes with similar code
     * Creation of new attribute with existing code.
     *
     * @param array $attrData
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-5353
     */
    public function withAttributeCodeThatAlreadyExists(array $attrData)
    {
        $this->markTestIncomplete('MAGETWO-8909');
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('error', 'exists_attribute_code');
    }

    /**
     * Checking validation for required field 'Attribute Label'
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3557
     */
    public function withRequiredAttributeLabelEmpty()
    {
        //Data
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_multiselect',
            array('attribute_label' => '%noValue%'));
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $fieldXpath = $this->_getControlXpath('field', 'attribute_label');
        $this->addParameter('fieldXpath', $fieldXpath);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * Checking validation for valid data in the 'Attribute Code' field
     *
     * @param $wrongAttributeCode
     * @param $validationMessage
     *
     * @test
     * @dataProvider withInvalidAttributeCodeDataProvider
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3554
     */
    public function withInvalidAttributeCode($wrongAttributeCode, $validationMessage)
    {
        //Data
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_multiselect',
            array('attribute_code' => $wrongAttributeCode));
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('validation', $validationMessage);
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withInvalidAttributeCodeDataProvider()
    {
        return array(
            array('11code_wrong', 'invalid_attribute_code'),
            array('CODE_wrong', 'invalid_attribute_code'),
            array('wrong code', 'invalid_attribute_code'),
            array($this->generate('string', 11, ':punct:'), 'invalid_attribute_code'),
            array($this->generate('string', 33, ':lower:'), 'wrong_length_attribute_code')
        );
    }

    /**
     * Checking validation for not valid data in the 'Position' field
     *
     * @param $invalidPosition
     *
     * @test
     * @dataProvider withInvalidPositionDataProvider
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3555
     */
    public function withInvalidPosition($invalidPosition)
    {
        //Data
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_multiselect',
            array('position' => $invalidPosition));
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('validation', 'error_invalid_position');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withInvalidPositionDataProvider()
    {
        return array(
            array('11code'),
            array('CODE11'),
            array('11 11'),
            array('11.11'),
            array('11,11'),
            array($this->generate('string', 10, ':punct:'))
        );
    }

    /**
     * Checking of correct validate of submitting form by using special
     * characters for all fields exclude 'Attribute Code' field.
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-5354
     */
    public function withSpecialCharactersInTitle()
    {
        //Data
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_multiselect',
            array('attribute_label' => $this->generate('string', 32, ':punct:')));
        $attrData['attribute_label'] = preg_replace('/<|>/', '', $attrData['attribute_label']);
        $searchData = $this->loadDataSet('ProductAttribute', 'attribute_search_data',
            array('attribute_code' => $attrData['attribute_code']));
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->productAttributeHelper()->openAttribute($searchData);
        //Verifying
        $this->productAttributeHelper()->verifyAttribute($attrData);
    }

    /**
     * Checking of correct work of submitting form by using long values for fields filling
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3556
     */
    public function withLongValues()
    {
        //Data
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_multiselect_with_options',
            array('attribute_code'  => $this->generate('string', 30, ':lower:'),
                  'attribute_label' => $this->generate('string', 255, ':alnum:'),
                  'position'        => 2147483647));
        $searchData = $this->loadDataSet('ProductAttribute', 'attribute_search_data',
            array('attribute_code'  => $attrData['attribute_code'],
                  'attribute_label' => $attrData['attribute_label']));
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->productAttributeHelper()->openAttribute($searchData);
        //Verifying
        $this->productAttributeHelper()->verifyAttribute($attrData);
    }
}
