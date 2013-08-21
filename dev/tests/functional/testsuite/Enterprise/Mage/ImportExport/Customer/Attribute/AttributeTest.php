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
 * Customer Attribute Export
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_ImportExport_Customer_Attribute_AttributeTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('export');
    }

    /**
     * Add customer attribute
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
        $this->importExportHelper()->customerFilterAttributes(
            array('attribute_code' => $attrData['attribute_properties']['attribute_code'])
        );
        //Step 5
        $isFound = $this->importExportHelper()->customerSearchAttributes(
            array('attribute_code' => $attrData['attribute_properties']['attribute_code']),
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
     *
     * @test
     * @param $attrData
     * @depends addCustomerAttribute
     * @TestlinkId TL-MAGE-5485
     */
    public function editCustomerAttribute($attrData)
    {
        //step1
        $this->navigate('manage_customer_attributes');
        $this->attributesHelper()->openAttribute(
            array('attribute_code'=>$attrData['attribute_properties']['attribute_code'])
        );
        //Change label
        $attrData['attribute_properties']['attribute_label'] = 'Text_Field_Admin_'
            . $this->generate('string', 5, ':lower:');
        $this->fillForm($attrData, 'manage_labels_options');
        $this->saveForm('save_attribute');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps 2-3
        $this->navigate('export');
        $this->importExportHelper()->chooseExportOptions('Customers Main File');
        //Step 4
        $this->importExportHelper()->customerFilterAttributes(
            array('attribute_code' => $attrData['attribute_properties']['attribute_code'])
        );
        //Step 5
        $isFound = $this->importExportHelper()->customerSearchAttributes(
            array('attribute_code' => $attrData['attribute_properties']['attribute_code']),
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
            array('attribute_code'=>$attrData['attribute_properties']['attribute_code']));
        //Delete attribute
        $this->clickButtonAndConfirm('delete_attribute', 'delete_confirm_message');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_attribute');
        //Steps 2-4
        $this->navigate('export');
        $this->importExportHelper()->chooseExportOptions('Customers Main File');
        //Step 5
        $this->importExportHelper()->customerFilterAttributes(
            array('attribute_code' => $attrData['attribute_properties']['attribute_code'])
        );
        //Step 6
        $isFound = $this->importExportHelper()->customerSearchAttributes(
            array('attribute_code' => $attrData['attribute_properties']['attribute_code']),
            'grid_and_filter'
        );
        $this->assertNull($isFound, 'Attribute was found after deleting');
        //Step 7
        $this->clickButton('reset_filter', false);
        $this->waitForAjax();
    }

    /**
     * Customer Master file export with using some filters
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
        $this->addParameter('attribute_name', $attrData['attribute_properties']['attribute_code']);
        $this->navigate('manage_customers');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $userData[$attrData['attribute_properties']['attribute_code']] =
            $attrData['attribute_properties']['default_value'];
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Steps 1-2
        $this->navigate('export');
        $this->assertTrue($this->checkCurrentPage('export'), $this->getParsedMessages());
        $this->importExportHelper()->chooseExportOptions('Customers Main File');
        //Step3
        $this->importExportHelper()->setFilter(
            array(
                 $attrData['attribute_properties']['attribute_code'] =>
                    $userData[$attrData['attribute_properties']['attribute_code']]
            )
        );
        //Step4-5
        $report = $this->importExportHelper()->export();
        //Verifying
        $this->assertNotNull($this->importExportHelper()->lookForEntity('master', $userData, $report),
            "Customer not found in csv file");
        $this->assertEquals(1, count($report), "Other customers are present in csv file");
    }
}
