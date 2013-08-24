<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Address Export
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_ImportExport_Customer_Address_AttributeTest extends Mage_Selenium_TestCase
{
    /**
     * Preconditions:
     * Log in to Backend.
     * Navigate to System -> Export/p>
     */
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
    }

    /**
     * Add customer address attribute
     *
     * @test
     * @TestlinkId TL-MAGE-5611
     * @return array
     */
    public function addCustomerAddressAttribute()
    {
        //step1
        $this->navigate('manage_customer_address_attributes');
        $attrData = $this->loadDataSet('CustomerAddressAttribute', 'customer_address_attribute_textfield',
            array('values_required' => 'No'));
        $this->attributesHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');

        return $attrData;
    }

    /**
     * Simple Export Address file with added address attribute (EE only)
     *
     * @test
     * @param array $attrData
     * @return array
     * @depends addCustomerAddressAttribute
     * @TestlinkId TL-MAGE-5611
     */
    public function simpleExportAddressFileWithCustomCustomerAddressAttribute($attrData)
    {
        //Precondition: create customer
        $this->navigate('manage_customers');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');

        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        //Step 1
        $this->navigate('export');
        //Step 2
        $this->importExportHelper()->chooseExportOptions('Customer Addresses');
        //Step 3
        $report = $this->importExportHelper()->export();
        //Verifying
        $this->assertNotNull($report, "Export csv file is empty");
        // search for new custom customer address attribute
        $this->assertArrayHasKey($attrData['attribute_properties']['attribute_code'], $report[0],
            'New custom customer address attribute is not present in export file'
        );

        return $attrData;
    }

    /**
     * Simple Export Address file with added address attribute (EE only)
     *
     * @test
     * @param array $attrData
     * @return array
     * @depends simpleExportAddressFileWithCustomCustomerAddressAttribute
     * @TestlinkId TL-MAGE-5501
     */
    public function simpleExportAddressFileWithDeletedCustomCustomerAddressAttribute($attrData)
    {
        //Precondition: delete custom address attribute
        $this->navigate('manage_customer_address_attributes');
        $this->attributesHelper()->openAttribute(
            array(
                'attribute_code' => $attrData['attribute_properties']['attribute_code'],
                'attribute_label' => $attrData['attribute_properties']['attribute_label']
            )
        );
        //Delete attribute
        $this->clickButtonAndConfirm('delete_attribute', 'delete_confirm_message');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_attribute');

        //Step 1
        $this->navigate('export');
        //Step 2
        $this->importExportHelper()->chooseExportOptions('Customer Addresses');
        //Step 3
        $report = $this->importExportHelper()->export();
        //Verifying
        $this->assertNotNull($report, "Export csv file is empty");
        // search for new custom customer address attribute
        $this->assertArrayNotHasKey($attrData['attribute_properties']['attribute_code'], $report[0],
            'Deleted custom customer address attribute is present in export file'
        );
    }
}
