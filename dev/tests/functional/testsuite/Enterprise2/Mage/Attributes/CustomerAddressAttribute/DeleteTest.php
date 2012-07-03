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
 * Delete Customer Address Attributes
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_Attributes_CustomerAddressAttribute_DeleteTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Customers  -> Attributes -> Manage Customer Address Attributes</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customer_address_attributes');
    }

    /**
     * <p>Delete Customer Address Attributes</p>
     * <p>Steps:</p>
     * <p>1.Click on "Add New Attribute" button</p>
     * <p>2.Fill all required fields</p>
     * <p>3.Click on "Save Attribute" button</p>
     * <p>4.Search and open attribute</p>
     * <p>5.Click on "Delete Attribute" button</p>
     * <p>Expected result:</p>
     * <p>Attribute successfully deleted.</p>
     * <p>Success message: 'The customer address attribute has been deleted.' is displayed.</p>
     *
     * @param $dataName
     *
     * @test
     * @dataProvider deleteProductAttributeDeletableDataProvider
     * @TestlinkId TL-MAGE-5596
     */
    public function deleteProductAttributeDeletable($dataName)
    {
        //Data
        $attrData = $this->loadDataSet('CustomerAddressAttribute', $dataName);
        $searchData = $this->loadDataSet('CustomerAddressAttribute', 'attribute_search_data',
            array('attribute_code' => $attrData['properties']['attribute_code']));
        //Steps
        $this->AttributesHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->AttributesHelper()->openAttribute($searchData);
        $this->clickButtonAndConfirm('delete_attribute', 'delete_confirm_message');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_attribute');
    }

    public function deleteProductAttributeDeletableDataProvider()
    {
        return array(
            array('customer_address_attribute_textfield'),
            array('customer_address_attribute_textarea'),
            array('customer_address_attribute_multipleline'),
            array('customer_address_attribute_date'),
            array('customer_address_attribute_dropdown'),
            array('customer_address_attribute_multiselect'),
            array('customer_address_attribute_yesno'),
            array('customer_address_attribute_attach_file'),
            array('customer_address_attribute_image_file')
        );
    }

    /**
     * <p>Delete system Customer Address Attributes</p>
     * <p>Steps:</p>
     * <p>1.Search and open system Customer Address Attributes.</p+>
     * <p>Expected result:</p>
     * <p>"Delete Attribute" button isn't present.</p>
     *
     * @param array $attributeName
     *
     * @test
     * @dataProvider deleteSystemAttributeDataProvider
     * @TestlinkId TL-MAGE-5597
    customerAddressAttributeHelper  */
    public function deletedSystemAttribute($attributeName)
    {
        //Data
        $searchData = $this->loadDataSet('CustomerAddressAttribute', 'attribute_search_data',
            array('attribute_code'  => $attributeName));
        //Steps
        $this->AttributesHelper()->openAttribute($searchData);
        //Verifying
        $this->assertFalse($this->buttonIsPresent('delete_attribute'),
            '"Delete Attribute" button is present on the page');
    }

    public function deleteSystemAttributeDataProvider()
    {
        return array(
            array('prefix'),
            array('firstname'),
            array('middlename'),
            array('lastname'),
            array('suffix'),
            array('company'),
            array('street'),
            array('city'),
            array('country_id '),
            array('region '),
            array('postcode'),
            array('telephone'),
            array('fax'),
            array('vat_id'),
        );
    }
}
