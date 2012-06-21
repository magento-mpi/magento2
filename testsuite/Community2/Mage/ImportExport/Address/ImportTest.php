<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer Export
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @method Enterprise2_Mage_CustomerAttribute_Helper customerAttributeHelper() customerAttributeHelper()
 * @method Enterprise2_Mage_CustomerAddressAttribute_Helper customerAddressAttributeHelper() customerAddressAttributeHelper()
 * @method Enterprise2_Mage_ImportExport_Helper importExportHelper() importExportHelper()
 */
class Community2_Mage_ImportExport_AddressImportTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     * <p>Navigate to System -> Export/p>
     */
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        //Step 1
        $this->navigate('import');
    }

    /**
     * <p>Required columns</p>
     * <p>Steps</p>
     * <p>1. Go to System -> Import / Export -> Import</p>
     * <p>2. Select Entity Type: Customers</p>
     * <p>3. Select Export Format Version: Magento 2.0 format</p>
     * <p>4. Select Customers Entity Type: Customer Addresses File</p>
     * <p>5. Choose file from precondition</p>
     * <p>6. Click on Check Data</p>
     * <p>7. Click on Import button</p>
     * <p>8. Open Customers -> Manage Customers</p>
     * <p>9. Open each of imported customers</p>
     * <p>After step 6</p>
     * <p>Verify that file is valid, the message 'File is valid!' is displayed</p>
     * <p>After step 7</p>
     * <p>Verify that import starting. The message 'Import successfully done.' is displayed</p>
     * <p>After step 9</p>
     * <p>Verify that all Customers address information was imported</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5624
     */
    public function importWithRequiredColumns()
    {
        //Precondition: create 2 new customers
        $this->admin('manage_customers');
        // 0.1. create customers with/o address
        $this->navigate('manage_customers');
        $userData2 = $this->loadDataSet('ImportExport', 'generic_customer_account');
        $addressData2 = $this->loadDataSet('ImportExport', 'generic_address');
        $this->customerHelper()->createCustomer($userData2, $addressData2);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Get existing address id to use in csv file
        $this->addParameter('customer_first_last_name', $userData2['first_name']. ' ' . $userData2['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userData2['email']));
        $this->openTab('addresses');
        $addressIdExisting = $this->customerHelper()->isAddressPresent($addressData2);
        $this->navigate('manage_customers');
        $userData1 = $this->loadDataSet('ImportExport', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData1);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->admin('import');
        //Step 1
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible(
            $this->_getControlXpath('dropdown', 'import_behavior')
        );
        $this->fillDropdown('import_behavior', 'Append Complex Data');
        $this->waitForElementVisible(
            $this->_getControlXpath('dropdown','import_file_version')
        );
        $this->fillDropdown('import_file_version', 'Magento 2.0 format');
        $this->waitForElementVisible(
            $this->_getControlXpath('dropdown', 'import_customer_entity')
        );
        $this->fillDropdown('import_customer_entity', 'Customer Addresses');
        //Generated CSV data
        $customerDataRow1 = $this->loadDataSet('ImportExport', 'import_address_file_required_fields1',
            array(
                '_entity_id' => $this->generate('string', 10, ':digit:'),
                '_email' => $userData1['email'],
            ));
        $unformattedStreet1 = $customerDataRow1['street'];
        $customerDataRow1['street'] = stripcslashes($customerDataRow1['street']);
        $customerDataRow2 = $this->loadDataSet('ImportExport', 'import_address_file_required_fields2',
            array(
                '_entity_id' => $addressIdExisting,
                '_email' => $userData2['email'],
            ));
        //Build CSV array
        $data = array(
            $customerDataRow1,
            $customerDataRow2
        );
        //Import file with default flow
        $report = $this->importExportHelper()->import($data) ;
        //Check import
        $this->assertArrayHasKey('import', $report, 'Import has been finished with issues:' .
            print_r($report) . print_r($data));
        $this->assertArrayHasKey('success', $report['import'], 'Import has been finished with issues:' .
            print_r($report) . print_r($data));
        //Check customers
        $this->admin('manage_customers');
        //Check updated customer
        $this->addParameter('customer_first_last_name',
            $userData1['first_name'] . ' ' . $userData1['last_name']);
        $this->customerHelper()->openCustomer(
            array(
                'email' => $userData1['email']
            ));
        $this->openTab('addresses');
        $addressData1 = array();
        $addressData1['city']       = $data[0]['city'];
        $addressData1['first_name'] = $data[0]['firstname'];
        $addressData1['last_name']  = $data[0]['lastname'];
        $addressData1['zip_code']   = $data[0]['postcode'];
        $addressData1['street_address_line_1'] = substr($unformattedStreet1, 0, strpos($unformattedStreet1, '\n'));
        $addressData1['street_address_line_2'] = substr($unformattedStreet1, strpos($unformattedStreet1, '\n') + 2);
        $addressData1['telephone']  = $data[0]['telephone'];
        print_r($addressData1);
        //Verify customer account address
        $this->assertNotEquals(0, $this->customerHelper()->isAddressPresent($addressData1),
            'New customer address has not been created');
        //Verify customer account
        $this->admin('manage_customers');
        $this->addParameter('customer_first_last_name',
            $userData2['first_name'] . ' ' . $userData2['last_name']);
        $this->customerHelper()->openCustomer(
            array(
                'email' => $userData2['email']
            ));
        $this->openTab('addresses');
        $addressData2['city']       = $data[1]['city'];
        $addressData2['first_name'] = $data[1]['firstname'];
        $addressData2['last_name']  = $data[1]['lastname'];
        $addressData2['zip_code']   = $data[1]['postcode'];
        $addressData2['street_address_line_1'] = $data[1]['street'];
        $addressData2['street_address_line_2'] = '';
        $addressData2['telephone']  = $data[1]['telephone'];
        $this->assertNotEquals(0, $this->customerHelper()->isAddressPresent($addressData2),
            'Existent customer address has not been updated');
    }

    /**
     * <p>Partial Import</p>
     * <p>Verify that if import file has some invalid data, then import will be finished partially</p>
     * <p>Precondition: two customers created in the system. Two csv files prepared. First one contains two rows:
     * valid customer address data, invalid customer address data (non existing website id). Second one contains two
     * rows: valid customer address data, valid customer address data (email is empty)</p>
     * <p>Steps</p>
     * <p>1. Go to System -> Import/ Export -> Import</p>
     * <p>2. In the drop-down "Entity Type" select "Customers"</p>
     * <p>3. Select "Magento 2.0 format"</p>
     * <p>4. Select Customers Entity Type: Customer Addresses File </p>
     * <p>5. Choose first file from precondition, click "Check Data" button, Press "Import" button</p>
     * <p>Expected: messages "Please fix errors and re-upload file or simply press "Import" button to skip rows with
     * errors" and "Checked rows: 2, checked entities: 2, invalid rows: 1, total errors: 1" are displayed</p>
     * <p>6. Choose second file from precondition, click "Check Data" button, Press "Import" button</p>
     * <p>Expected: messages "E-mail is not specified in rows: 2" and "Please fix errors and re-upload
     * file or simply press "Import" button to skip rows with errors", "Checked rows: 2, checked entities: 2, invalid
     * rows: 1, total errors: 1" are displayed</p>
     * <p>7. Open customers</p>
     * <p>Expected: valid address data information was imported correctly</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5636
     */
    public function partialImport()
    {
        //Precondition
        $customerData[0] = $this->loadDataSet('ImportExport.yml', 'data_valid_customer1_5635');
        $customerData[1] = $this->loadDataSet('ImportExport.yml', 'data_valid_customer1_5635');
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($customerData[0]);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->createCustomer($customerData[1]);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $addressData[0]['valid'] = $this->loadDataSet('ImportExport.yml', 'data_valid_address1_5636');
        $addressData[1]['valid'] = $this->loadDataSet('ImportExport.yml', 'data_valid_address2_5636');
        $csvData[0]['valid'] = $this->loadDataSet('ImportExport.yml', 'csv_valid_address1_5636');
        $csvData[0]['invalid'] = $this->loadDataSet('ImportExport.yml', 'csv_invalid_address1_5636');
        $csvData[1]['valid'] = $this->loadDataSet('ImportExport.yml', 'csv_valid_address2_5636');
        foreach($csvData as $key => $value) {
                foreach($csvData[$key] as $key2 => $value2) {
                    $csvData[$key][$key2]['_entity_id'] = $this->generate('string', 10, ':digit:');
                    $csvData[$key][$key2]['_email'] = $customerData[0]['email'];
                }
        }
        $csvData[1]['valid']['_email'] = $customerData[1]['email'];
        $csvData[1]['invalid'] = $csvData[1]['valid'];
        $csvData[1]['invalid']['_email'] = '';
        //Step 1
        $this->admin('import');
        //Step 2
        $this->fillDropdown('entity_type', 'Customers');
        $this->fillDropdown('import_behavior', 'Append Complex Data');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'import_file_version'));
        //Step 3
        $this->fillDropdown('import_file_version', 'Magento 2.0 format');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'import_customer_entity'));
        //Step 4
        $this->fillDropdown('import_customer_entity', 'Customer Addresses');
        //Step 5
        $importData = $this->importExportHelper()->import($csvData[0]);
        //Verifying
        $this->assertEquals(
            'Invalid value in Website column (website does not exists?) in rows: 2',
            $importData['validation']['error'][0], 'Message about invalid website is absent'
        );
        $this->assertEquals(
            'Please fix errors and re-upload file or simply press "Import" button to skip rows with errors  Import',
            $importData['validation']['validation'][0], 'No message about possibility to fix errors or continue'
        );
        $this->assertEquals(
            'Checked rows: 2, checked entities: 2, invalid rows: 1, total errors: 1',
            $importData['validation']['validation'][1], 'No message checked rows number'
        );
        //Step 6
        $importData = $this->importExportHelper()->import($csvData[1]);
        //Verifying messages (second file)
        $this->assertEquals(
            'E-mail is not specified in rows: 2',
            $importData['validation']['error'][0], 'Message about duplication is absent'
        );
        $this->assertEquals(
            'Please fix errors and re-upload file or simply press "Import" button to skip rows with errors  Import',
            $importData['validation']['validation'][0], 'No message about possibility to fix errors or continue'
        );
        $this->assertEquals(
            'Checked rows: 2, checked entities: 2, invalid rows: 1, total errors: 1',
            $importData['validation']['validation'][1], 'No message checked rows number'
        );
        //Step 7
        $this->admin('manage_customers');
        $this->addParameter('customer_first_last_name', $customerData[0]['first_name'] . ' '
            . $customerData[0]['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $customerData[0]['email']));
        $this->openTab('addresses');
        //Verifying if new address is present (first file)
        $this->assertNotEquals(0, $this->customerHelper()->isAddressPresent($addressData[0]['valid']),
            'New customer address has not been created');

        $this->admin('manage_customers');
        $this->addParameter('customer_first_last_name', $customerData[1]['first_name'] . ' '
            . $customerData[1]['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $customerData[1]['email']));
        $this->openTab('addresses');
        //Verifying if new address is present (second file)
        $this->assertNotEquals(0, $this->customerHelper()->isAddressPresent($addressData[1]['valid']),
            'New customer address has not been created');
    }
}
