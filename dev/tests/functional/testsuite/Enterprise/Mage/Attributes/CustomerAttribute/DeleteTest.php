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
 * Delete Customer  Attributes
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_Attributes_CustomerAttribute_DeleteTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Customers -> Attributes -> Manage Customer  Attributes</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customer_attributes');
    }

    /**
     * <p>Delete Customer Attributes</p>
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
        $attrData = $this->loadDataSet('CustomerAttribute', $dataName);
        $searchData = $this->loadDataSet('CustomerAttribute', 'customer_attribute_search_data',
            array('attribute_code' => $attrData['attribute_properties']['attribute_code']));
        //Steps
        $this->attributesHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->attributesHelper()->openAttribute($searchData);
        $this->clickButtonAndConfirm('delete_attribute', 'delete_confirm_message');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_attribute');
    }

    public function deleteProductAttributeDeletableDataProvider()
    {
        return array(
            array('customer_attribute_textfield'),
            array('customer_attribute_textarea'),
            array('customer_attribute_multipleline'),
            array('customer_attribute_date'),
            array('customer_attribute_dropdown'),
            array('customer_attribute_multiselect'),
            array('customer_attribute_yesno'),
            array('customer_attribute_attach_file'),
            array('customer_attribute_image_file')
        );
    }

    /**
     * <p>Delete system  Customer Attributes</p>
     *
     * @param array $attributeName
     *
     * @test
     * @dataProvider deleteSystemAttributeDataProvider
     * @TestlinkId TL-MAGE-5597
     */
    public function deletedSystemAttribute($attributeName)
    {
        $this->markTestIncomplete('MAGETWO-8975');
        //Data
        $searchData = $this->loadDataSet('CustomerAttribute', 'customer_attribute_search_data',
            array('attribute_code'  => $attributeName));
        //Steps
        $this->attributesHelper()->openAttribute($searchData);
        //Verifying
        $this->assertFalse($this->buttonIsPresent('delete_attribute'),
            '"Delete Attribute" button is present on the page');
    }

    public function deleteSystemAttributeDataProvider()
    {
        return array(
            array('created_at'),
            array('reward_update_notification'),
            array('reward_warning_notification'),
            array('website_id'),
            array('created_in'),
            array('group_id'),
            array('prefix'),
            array('firstname'),
            array('middlename'),
            array('lastname'),
            array('suffix'),
            array('email'),
            array('dob'),
            array('taxvat'),
            array('gender'),
        );
    }
}
