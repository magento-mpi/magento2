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
 * Customer Addresses Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_ImportExport_Import_AddressTest extends Mage_Selenium_TestCase
{
    protected static $customerData = array();

    /**
     * <p>Precondition:</p>
     * <p>Create 2 customers</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->admin('manage_customers');
        self::$customerData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer(self::$customerData);
        $this->assertMessagePresent('success', 'success_saved_customer');
    }
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
        $userData2 = $this->loadDataSet('Customers', 'generic_customer_account');
        $addressData2 = $this->loadDataSet('Customers', 'generic_address');
        $this->customerHelper()->createCustomer($userData2, $addressData2);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Get existing address id to use in csv file
        $this->addParameter('customer_first_last_name', $userData2['first_name']. ' ' . $userData2['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userData2['email']));
        $this->openTab('addresses');
        $addressIdExisting = $this->customerHelper()->isAddressPresent($addressData2);
        $this->navigate('manage_customers');
        $userData1 = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData1);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->admin('import');
        //Step 1
        $this->importExportHelper()->chooseImportOptions('Customers', 'Add/Update Complex Data',
            'Magento 2.0 format', 'Customer Addresses');
        //Generated CSV data
        $customerDataRow1 = $this->loadDataSet('ImportExport', 'generic_address_csv',
                                            array(
                                                '_entity_id' => '',
                                                '_email' => $userData1['email'],
                                                'city' => 'Lincoln',
                                                'country_id' => 'US',
                                                'firstname' => 'Jana',
                                                'lastname' => 'Johnson',
                                                'postcode' => '90232',
                                                'street' => "4955 Crummit\nLane",
                                                'telephone' => '402-219-4835'
                                            )
        );
        $unformattedStreet1 = $customerDataRow1['street'];
        $customerDataRow1['street'] = stripcslashes($customerDataRow1['street']);
        $customerDataRow2 = $this->loadDataSet('ImportExport', 'generic_address_csv',
                                            array(
                                                '_entity_id' => $addressIdExisting,
                                                '_email' => $userData2['email'],
                                                'city' => 'Milwaukee',
                                                'country_id' => 'US',
                                                'firstname' => 'Kim',
                                                'lastname' => 'Montgomery',
                                                'postcode' => '53213',
                                                'street' => "593 Grant View Drive",
                                                'telephone' => '414-411-2378'
                                            )
        );
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
        $addressData1 = array(
            'city'       => $data[0]['city'],
            'first_name' => $data[0]['firstname'],
            'last_name'  => $data[0]['lastname'],
            'zip_code'   => $data[0]['postcode'],
            'street_address_line_1' => substr($unformattedStreet1, 0, strpos($unformattedStreet1, "\n")),
            'street_address_line_2' => substr($unformattedStreet1, strpos($unformattedStreet1, "\n") + 1),
            'telephone'  => $data[0]['telephone']
        );
        //Verify customer account address
        $this->assertTrue((bool) $this->customerHelper()->isAddressPresent($addressData1),
            'New customer address has not been created '. print_r($addressData1));
        //Verify customer account
        $this->admin('manage_customers');
        $this->addParameter('customer_first_last_name',
            $userData2['first_name'] . ' ' . $userData2['last_name']);
        $this->customerHelper()->openCustomer(
            array(
                'email' => $userData2['email']
            ));
        $addressData2['city']       = $data[1]['city'];
        $addressData2['first_name'] = $data[1]['firstname'];
        $addressData2['last_name']  = $data[1]['lastname'];
        $addressData2['zip_code']   = $data[1]['postcode'];
        $addressData2['street_address_line_1'] = $data[1]['street'];
        $addressData2['street_address_line_2'] = '';
        $addressData2['telephone']  = $data[1]['telephone'];
        $addressData2['state']  = $data[1]['region'];
        $this->assertTrue((bool) $this->customerHelper()->isAddressPresent($addressData2),
            'Existent customer address has not been updated ' . print_r($addressData2));
    }

    /**
     * <p>Partial Import</p>
     * <p>Verify that if import file has some invalid data, then import will be finished partially</p>
     * <p>Precondition: one customer created in the system. Two csv files prepared. First one contains two rows:
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
     * @dataProvider partialImportData
     * @TestlinkId TL-MAGE-5636
     */
    public function partialImport($csvData, $newAddressData, $validation)
    {
        //Set correct email for csv data
        foreach ($csvData as $key => $value) {
            if (array_key_exists('_email', $csvData[$key]) && $csvData[$key]['_email'] == '<realEmail>'){
                $csvData[$key]['_email'] = self::$customerData['email'];
            }
        }
        //Step 1
        $this->admin('import');
        //Steps 2-4
        $this->importExportHelper()->chooseImportOptions('Customers', 'Add/Update Complex Data',
            'Magento 2.0 format', 'Customer Addresses');
        //Steps 5-6
        $importData = $this->importExportHelper()->import($csvData);
        //Verifying import
        $this->assertEquals($validation, $importData, 'Import has been finished with issues');
        //Step 7
        $this->admin('manage_customers');
        $this->addParameter('customer_first_last_name', self::$customerData['first_name'] . ' '
            . self::$customerData['last_name']);
        $this->customerHelper()->openCustomer(array('email' => self::$customerData['email']));
        $this->openTab('addresses');
        //Verifying if new address is present
        $this->assertNotEquals(0, $this->customerHelper()->isAddressPresent($newAddressData),
            'New customer address has not been created');
    }

    public function partialImportData()
    {
        $csvRow1File1 = $this->loadDataSet('ImportExport', 'generic_address_csv', array(
                '_email' => '<realEmail>',
                'city' => 'Camden',
                'company' => 'Team Electronics',
                'fax' => '609-504-6350',
                'firstname' => 'William',
                'lastname' => 'Holler',
                'middlename' => 'E.',
                'postcode' => '08102',
                'prefix' => '',
                'region' => 'New Jersey',
                'street' => '3186 Lincoln Street',
                'telephone' => '609-504-6350',
            )
        );
        $newAddress1Data = $this->loadDataSet('Customers', 'generic_address', array(
                'first_name' => 'William',
                'middle_name' => 'E.',
                'last_name' => 'Holler',
                'company' => 'Team Electronics',
                'street_address_line_1' => '3186 Lincoln Street',
                'street_address_line_2' => '',
                'city' => 'Camden',
                'state' => 'New Jersey',
                'zip_code' => '08102',
                'telephone' => '609-504-6350',
                'fax' => '609-504-6350',
            )
        );
        $csvRow2File1 = $this->loadDataSet('ImportExport', 'generic_address_csv', array(
                '_email' => '<realEmail>',
                '_website' => 'invalid',
            )
        );

        $csvRows1 = array($csvRow1File1, $csvRow2File1);

        $messageFile1 = array('validation' => array(
            'error' => array("Invalid value in website column in rows: 2"),
            'validation' => array(
                "Please fix errors and re-upload file or simply press \"Import\" button to skip rows with errors  Import",
                "Checked rows: 2, checked entities: 2, invalid rows: 1, total errors: 1",
            )
        ),
            'import' => array(
                'success' => array("Import successfully done."),
            ),
        );

        $csvRow1File2 = $this->loadDataSet('ImportExport', 'generic_address_csv', array(
                '_email' => '<realEmail>',
                'city' => 'San Antonio',
                'company' => 'Wherehouse Music',
                'fax' => '210-315-4837',
                'firstname' => 'Cora',
                'lastname' => 'Robles',
                'middlename' => 'K.',
                'postcode' => '78258',
                'prefix' => '',
                'region' => 'Texas',
                'street' => '1506 Weekley Street',
                'telephone' => '210-315-4837',
            )
        );
        $newAddress2Data = $this->loadDataSet('Customers', 'generic_address', array(
                'first_name' => 'Cora',
                'middle_name' => 'K.',
                'last_name' => 'Robles',
                'company' => 'Wherehouse Music',
                'street_address_line_1' => '1506 Weekley Street',
                'street_address_line_2' => '',
                'city' => 'San Antonio',
                'state' => 'Texas',
                'zip_code' => '78258',
                'telephone' => '210-315-4837',
                'fax' => '210-315-4837',
            )
        );
        $csvRow2File2 = $this->loadDataSet('ImportExport', 'generic_address_csv', array(
                '_email' => '',
            )
        );

        $csvRows2 = array($csvRow1File2, $csvRow2File2);

        $messageFile2 = array('validation' => array(
            'error' => array("E-mail is not specified in rows: 2"),
            'validation' => array(
                "Please fix errors and re-upload file or simply press \"Import\" button to skip rows with errors  Import",
                "Checked rows: 2, checked entities: 2, invalid rows: 1, total errors: 1",
            )
        ),
            'import' => array(
                'success' => array("Import successfully done."),
            ),
        );

        return array(
            array($csvRows1, $newAddress1Data, $messageFile1),
            array($csvRows2, $newAddress2Data, $messageFile2),
        );

    }
}
