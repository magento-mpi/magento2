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

class Enterprise_Mage_Rma_ItemAttribute_CreateTest extends Mage_Selenium_TestCase
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
     *
     * @test
     * @TestlinkId TL-MAGE-6110
     */
    public function withAttributeCodeThatAlreadyExists()
    {
        $this->markTestIncomplete('MAGETWO-9471');
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
