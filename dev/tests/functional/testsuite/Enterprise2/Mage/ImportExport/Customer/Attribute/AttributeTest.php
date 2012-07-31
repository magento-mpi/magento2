<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Attribute Export
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_ImportExport_Attribute_CustomerTest extends Mage_Selenium_TestCase
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
        //Step 1
        $this->navigate('export');
    }

    /**
     * Add customer attribute
     * Steps
     * 1. Admin is logged in at backend
     * 2. New Customers Attribute is created in Customers -> Attributes -> Manage Customers Attributes
     * 3. In System-> Import/Export-> Export select "Master Type" file
     *
     * @test
     * @TestlinkId TL-MAGE-5484
     * @return array
     */
    public function addCustomerAttribute()
    {
        //Step 2
        $this->navigate('manage_customer_attributes');
        $attrData = $this->loadDataSet('CustomerAttribute', 'customer_attribute_textfield',
            array('values_required' => 'No'));
        $this->attributesHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Step 3
        $this->navigate('export');
        $this->importExportHelper()->chooseExportOptions('Customers Main File');
        //Step 4
        $this->ImportExportHelper()->customerFilterAttributes(
            array(
                'attribute_code' => $attrData['properties']['attribute_code']
            )
        );
        //Step 5
        $isFound = $this->ImportExportHelper()->customerSearchAttributes(
            array(
                'attribute_code' => $attrData['properties']['attribute_code']
            ),
            'grid_and_filter'
        );
        $this->assertNotNull($isFound, 'Attribute was not found after filtering');
        //Step 6
        $this->clickButton('reset_filter', false);
        $this->waitForAjax();
        return $attrData;
    }

    /**
     * Edit customer attribute
     * Steps
     * 1. Admin is logged in at backend
     * In Customers->Attributes->ManageCustomersAttributes change info in field "Attribute Label" for existing Attribute
     * 3. In System-> Import/Export-> Export select "Master Type" file
     *
     * @test
     * @param array $attrData
     * @depends addCustomerAttribute
     * @TestlinkId TL-MAGE-5485
     */
    public function editCustomerAttribute($attrData)
    {
        //step1
        $this->navigate('manage_customer_attributes');
        $this->attributesHelper()->openAttribute(
            array(
                'attribute_code'=>$attrData['properties']['attribute_code']));
        //Change label
        $attrData['manage_labels_options']['admin_title'] = 
            'Text_Field_Admin_' . $this->generate('string', 5, ':lower:');
        $this->attributesHelper()->fillForm($attrData, 'manage_labels_options');
        $this->attributesHelper()->saveForm('save_attribute');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps 2-3
        $this->navigate('export');
        $this->importExportHelper()->chooseExportOptions('Customers Main File');
        //Step 4
        $this->ImportExportHelper()->customerFilterAttributes(
            array(
                'attribute_code' => $attrData['properties']['attribute_code']
            ));
        //Step 5
        $isFound = $this->ImportExportHelper()->customerSearchAttributes(
            array(
                'attribute_code' => $attrData['properties']['attribute_code']),
            'grid_and_filter'
            );
        $this->assertNotNull($isFound, 'Attribute was not found after filtering');
        //Step 6
        $this->clickButton('reset_filter', false);
        $this->waitForAjax();
        return $attrData;
    }

    /**
     * Edit customer attribute
     * Steps
     * 1. Admin is logged in at backend
     * 2. Create new customer attribute in Customers -> Attributes -> Manage Customers Attributes
     * 3. Delete the attribute from precondition 2 in Customers -> Attributes -> Manage Customers Attributes
     * 4. In System-> Import/Export-> Export select "Master Type" file
     *
     * @test
     * @param array $attrData
     * @depends editCustomerAttribute
     * @TestlinkId TL-MAGE-5486
     */
    public function deleteCustomerAttribute($attrData)
    {
        //step1
        $this->navigate('manage_customer_attributes');
        $this->attributesHelper()->openAttribute(
            array(
                'attribute_code'=>$attrData['properties']['attribute_code']));
        //Delete attribute
        $this->clickButtonAndConfirm('delete_attribute', 'delete_confirm_message');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_attribute');
        //Steps 2-4
        $this->navigate('export');
        $this->importExportHelper()->chooseExportOptions('Customers Main File');
        //Step 5
        $this->ImportExportHelper()->customerFilterAttributes(
            array(
                'attribute_code' => $attrData['properties']['attribute_code']
                )
        );
        //Step 6
        $isFound = $this->ImportExportHelper()->customerSearchAttributes(
            array(
                'attribute_code' => $attrData['properties']['attribute_code']
                ),
            'grid_and_filter'
        );
        $this->assertNull($isFound, 'Attribute was found after deleting');
        //Step 7
        $this->clickButton('reset_filter', false);
        $this->waitForAjax();
    }

    /**
     * Customer Master file export with using some filters
     * Steps
     * 1. On backend in System -> Import/ Export -> Export select "Master Type File"
     * 2. In the "Filter" column according to you attribute select option that was used in your customer creation
     * 3. Press "Continue" button and save current file
     * 4. Open file
     * Expected: In generated file just your customer with selected option of attribute is present
     *
     * @test
     * @TestlinkId TL-MAGE-5488
     */
    public function exportMasterFileWithFilters()
    {
        //Precondition: create attribute, create new customer, fill created attribute
        $this->navigate('manage_customer_attributes');
        $attrData = $this->loadDataSet('CustomerAttribute', 'customer_attribute_textfield',
            array('values_required' => 'No', 'default_value' => 'default text ' . $this->generate('string', 5)));
        $this->attributesHelper()->createAttribute($attrData);
        $this->addParameter('attribute_name', $attrData['properties']['attribute_code']);
        $this->navigate('manage_customers');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $userData[$attrData['properties']['attribute_code']] = $attrData['properties']['default_value'];
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Steps 1-2
        $this->navigate('export');
        $this->assertTrue($this->checkCurrentPage('export'), $this->getParsedMessages());
        $this->importExportHelper()->chooseExportOptions('Customers Main File');
        //Step3
        $this->ImportExportHelper()->setFilter(array(
                $attrData['properties']['attribute_code'] => $userData[$attrData['properties']['attribute_code']])
        );
        //Step4-5
        $report = $this->ImportExportHelper()->export();
        //Verifying
        $this->assertNotNull($this->importExportHelper()->lookForEntity('master', $userData, $report),
            "Customer not found in csv file");
        $this->assertEquals(1, count($report), "Other customers are present in csv file");
    }

}