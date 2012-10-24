<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_RMA
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Create RMA item attribute
 *
 * @package     Mage_RMA
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise2_Mage_RMA_ItemAttribute_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Manage Attributes.</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_rma_items_attribute');
    }

    /**
     * <p>Create All types RMA item Attribute (required fields only)</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend</p>
     * <p>2. Navigate to Sales-RMA-Manage RMA Items Attributes<p>
     * <p>3. Click on "Add New Attribute" button</p>
     * <p>4. Choose "Text Field" in 'Input Type' dropdown</p>
     * <p>5. Fill all required fields</p>
     * <p>6. Click "Save Attribute" button</p>
     * <p>7. Repeat steps 3-6 used other attribute type: Text Area, Dropdown, Image File</p>
     * <p>Expected result:</p>
     * <p>Success message: 'The RMA item attribute has been saved.' is displayed.</p>
     *
     * @test
     * @dataProvider allAttributeTypeDataProvider
     * @TestlinkId TL-MAGE-6109
     */
    public function allAttributeType($attributeType)
    {
        //Data
        $attrData = $this->loadDataSet('RMAItemsAttribute', $attributeType);
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_attribute');
    }

    public function allAttributeTypeDataProvider()
    {
        return array(
            array('rma_item_attribute_textfield'),
            array('rma_item_attribute_textarea'),
            array('rma_item_attribute_dropdown'),
            array('rma_item_attribute_image')
        );
    }

    /**
     * <p>Create attribute with existing attribute code</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend</p>
     * <p>2. Navigate to Sales-RMA-Manage RMA Items Attributes<p>
     * <p>3. Create any attribute</p>
     * <p>4. Try create attribute with same attribute code</p>
     * <p>Expected result:</p>
     * <p>Error message: 'Attribute with the same code already exists' is displayed.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6110
     */
    public function withAttributeCodeThatAlreadyExists()
    {
        //Data
        $attrData = $this->loadDataSet('RMAItemsAttribute', 'rma_item_attribute_textfield');
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->validatePage('new_rma_item_attribute');
        $this->assertMessagePresent('error', 'exists_attribute_code');
    }

    /**
     * <p>Checking validation for required fields are EMPTY</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend</p>
     * <p>2. Navigate to Sales-RMA-Manage RMA Items Attributes<p>
     * <p>3. Click on "Add New Attribute" button</p>
     * <p>4. CChoose "Text Field" in 'Input Type' dropdown</p>
     * <p>5. Skip filling of one field required and fill other required fields.</p>
     * <p>6. Click on "Save Attribute" button</p>
     * <p>7. Repeat steps 4-6 with other required fields</p>
     * <p>Expected result:</p>
     * <p>New attribute should not be created.</p>
     * <p>Error JS message: 'This is a required field.' is displayed.</p>
     *
     * @param $emptyField
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @TestlinkId TL-MAGE-6111
     */
    public function withEmptyRequiredFields($emptyField)
    {
        //Data
        $attrData = $this->loadDataSet('RMAItemsAttribute', 'rma_item_attribute_textfield',
            array($emptyField => '%noValue%'));
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        $message = 'empty_' . $emptyField;
        //Verifying
        $this->assertMessagePresent('validation', $message);
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('attribute_code'),
            array('sort_order'),
            array('admin_title')
        );
    }

    /**
     * <p>Checking validation in the 'Attribute Code' field</p>
     * <p>Steps:</p>
     * <p>1. Login to Backend</p>
     * <p>2. Navigate to Sales-RMA-Manage RMA Items Attributes<p>
     * <p>3. Click on "Add New Attribute" button</p>
     * <p>4. Fill 'Attribute Code' field by invalid data [Examples: '0xxx'/'_xxx'/'111']</p>
     * <p>5. Fill other required fields by regular data.</p>
     * <p>6. Click on "Save Attribute" button</p>
     * <p>Expected result:</p>
     * <p>New attribute ["Text Field" type] should not be created.</p>
     * <p>Error JS message: 'Please use only letters (a-z), numbers (0-9) or underscore(_) in
     * this field, first character should be a letter.' is displayed.</p>
     *
     * @param $wrongAttributeCode
     * @param $validationMessage
     *
     * @test
     * @dataProvider withInvalidAttributeCodeDataProvider
     * @TestlinkId TL-MAGE-6112
     */
    public function withInvalidAttributeCode($wrongAttributeCode, $validationMessage)
    {
        //Data
        $attrData = $this->loadDataSet('RMAItemsAttribute', 'rma_item_attribute_textfield',
            array('attribute_code' => $wrongAttributeCode));
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('validation', $validationMessage);
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
}
