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
class Enterprise_Mage_Attributes_CustomerAddressAttribute_DeleteTest extends Mage_Selenium_TestCase
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
        $searchData = $this->loadDataSet('CustomerAddressAttribute', 'customer_address_attribute_search_data',
            array('attribute_code' => $attrData['attribute_properties']['attribute_code']));
        //Steps
        $this->attributesHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->addParameter('elementTitle', $attrData['attribute_properties']['attribute_label']);
        $this->attributesHelper()->openAttribute($searchData);
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
     *
     * @param array $attributeCode
     * @param array $attributeName
     *
     * @test
     * @dataProvider deleteSystemAttributeDataProvider
     * @TestlinkId TL-MAGE-5597
     */
    public function deletedSystemAttribute($attributeCode, $attributeName)
    {
        //Data
        $searchData = $this->loadDataSet('CustomerAddressAttribute', 'customer_address_attribute_search_data',
            array('attribute_code'  => $attributeCode));
        //Steps
        $this->addParameter('elementTitle', $attributeName);
        $this->attributesHelper()->openAttribute($searchData);
        //Verifying
        $this->assertFalse($this->buttonIsPresent('delete_attribute'),
            '"Delete Attribute" button is present on the page');
    }

    public function deleteSystemAttributeDataProvider()
    {
        return array(
            array('prefix', 'Prefix'),
            array('firstname', 'First Name'),
            array('middlename', 'Middle Name/Initial'),
            array('lastname', 'Last Name'),
            array('suffix', 'Suffix'),
            array('company', 'Company'),
            array('street', 'Street Address'),
            array('city', 'City'),
            array('country_id', 'Country'), //need to clarify attribute name
            array('region', 'State/Province'), //need to clarify attribute name
            array('postcode', 'Zip/Postal Code'),
            array('telephone', 'Telephone'),
            array('fax', 'Fax'),
            array('vat_id', 'VAT number'),
        );
    }
}
