<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Attributes
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Create new customer address attribute. Type: Yes/No
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_Attributes_CustomerAddressAttribute_Create_YesNoTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Customer -> Attributes -> Manage Customer Address Attributes</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customer_address_attributes');
    }

    /**
     * @test
     */
    public function navigation()
    {
        $this->assertTrue($this->buttonIsPresent('add_new_attribute'),
            'There is no "Add New Attribute" button on the page');
        $this->clickButton('add_new_attribute');
        $this->assertTrue($this->checkCurrentPage('new_customer_address_attribute'), $this->getParsedMessages());
        $this->assertTrue($this->buttonIsPresent('back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_attribute'), 'There is no "Save" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_and_continue_edit'),
            'There is no "Save and Continue Edit" button on the page');
    }

    /**
     * <p>Create "Yes/No" type Customer Address Attribute (required fields only)</p>
     *
     * @return array
     * @test
     * @depends navigation
     * @TestlinkId TL-MAGE-5587
     */
    public function withRequiredFieldsOnly()
    {
        //Data
        $attrData = $this->loadDataSet('CustomerAddressAttribute', 'customer_address_attribute_yesno');
        //Steps
        $this->attributesHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');

        return $attrData;
    }

    /**
     * <p>Checking of verification for duplicate of Customer Address Attributes with similar code
     * Creation of new attribute with existing code.</p>
     *
     * @param array $attrData
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-5588
     */
    public function withAttributeCodeThatAlreadyExists(array $attrData)
    {
        //Steps
        $this->attributesHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('error', 'exists_attribute_code');
    }

    /**
     * <p>Checking validation for required fields are EMPTY</p>
     *
     * @param $emptyField
     * @param $messageCount
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @TestlinkId TL-MAGE-5589
     */
    public function withRequiredFieldsEmpty($emptyField, $messageCount)
    {
        //Data
        $attrData = $this->loadDataSet('CustomerAddressAttribute', 'customer_address_attribute_yesno',
            array($emptyField => '%noValue%'));
        //Steps
        $this->attributesHelper()->createAttribute($attrData);
        //Verifying
        $xpath = $this->_getControlXpath('field', $emptyField);
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount($messageCount), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('attribute_code', 1),
            array('sort_order', 1),
            array('admin_title', 1)
        );
    }

    /**
     * <p>Checking validation for valid data in the 'Attribute Code' field</p>
     *
     * @param $wrongAttributeCode
     * @param $validationMessage
     *
     * @test
     * @dataProvider withInvalidAttributeCodeDataProvider
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-5590
     */
    public function withInvalidAttributeCode($wrongAttributeCode, $validationMessage)
    {
        //Data
        $attrData = $this->loadDataSet('CustomerAddressAttribute', 'customer_address_attribute_yesno',
            array('attribute_code' => $wrongAttributeCode));
        //Steps
        $this->attributesHelper()->createAttribute($attrData);
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
     * <p>Checking of correct validate of submitting form by using special
     * characters for all fields exclude 'Attribute Code' field.</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-5591
     */
    public function withSpecialCharactersInTitle()
    {
        //Data
        $attrData = $this->loadDataSet('CustomerAddressAttribute', 'customer_address_attribute_yesno',
            array('admin_title' => $this->generate('string', 32, ':punct:')));
        $attrData['manage_labels_options']['admin_title'] = preg_replace('/<|>/', '',
            $attrData['manage_labels_options']['admin_title']);
        $searchData = $this->loadDataSet('CustomerAddressAttribute', 'customer_address_attribute_search_data',
            array('attribute_code' => $attrData['properties']['attribute_code']));
        //Steps
        $this->attributesHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->addParameter('elementTitle', $attrData['manage_labels_options']['admin_title']);
        $this->attributesHelper()->openAttribute($searchData);
        //Verifying
        $this->productAttributeHelper()->verifyAttribute($attrData);
    }

    /**
     * <p>Checking of correct work of submitting form by using long values for fields filling</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-5592
     */
    public function withLongValues()
    {
        //Data
        $attrData = $this->loadDataSet('CustomerAddressAttribute', 'customer_address_attribute_yesno',
            array('attribute_code' => $this->generate('string', 21, ':lower:'),
            'admin_title'    => $this->generate('string', 255, ':alnum:')));
        $searchData = $this->loadDataSet('CustomerAddressAttribute', 'customer_address_attribute_search_data',
            array('attribute_code'  => $attrData['properties']['attribute_code'],
                  'attribute_label' => $attrData['manage_labels_options']['admin_title']));
        //Steps
        $this->attributesHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->addParameter('elementTitle', $attrData['manage_labels_options']['admin_title']);
        $this->attributesHelper()->openAttribute($searchData);
        //Verifying
        $this->productAttributeHelper()->verifyAttribute($attrData);
    }
}