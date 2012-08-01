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
 * Customer Backward Compatibility Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_ImportExport_Backward_Import_CustomerTest extends Mage_Selenium_TestCase
{
    protected static $_customerData = array();
    protected static $_addressData = array();

    /**
     * Precondition:
     * Create new customer
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        self::$_customerData = $this->loadDataSet('Customers', 'generic_customer_account');
        self::$_addressData = $this->loadDataSet('Customers', 'generic_address');
        $this->customerHelper()->createCustomer(self::$_customerData, self::$_addressData);
        $this->assertMessagePresent('success', 'success_saved_customer');
    }

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
        $this->navigate('import');
    }

    /**
     * Has been excluded from functionality scope
     * Validation Result block
     * Verify that Validation Result block will be displayed after checking data of import file
     * Precondition: at least one customer exists, one file is generated after export
     * Steps:
     * 1. Go to System -> Import/ Export -> Import
     * 2. In the drop-down "Entity Type" select "Customers"
     * 3. In "Import Format Version" dropdown field choose "Magento 1.7 format" parameter
     * 4. In "Import Behavior" dropdown field choose "Append Complex Data" parameter
     * 5. Select file to import
     * 6. Click "Check Data" button.
     * Expected: validation and success messages are correct
     *
     * @test
     * @TestlinkId TL-MAGE-1108
     * @group skip_due_to_bug
     */
    public function validationResultBlock()
    {
        //Precondition
        $this->navigate('export');
        $this->importExportHelper()->chooseExportOptions('Customers', 'Magento 1.7 format');
        $report = $this->importExportHelper()->export();
        //calculate number of entities in csv file
        $numberOfEntities = 0;
        foreach ($report as $value) {
            if ($value['email'] != '') {
                $numberOfEntities++;
            }
        }
        //Step 1
        $this->navigate('import');
        //Steps 2-4
        $this->importExportHelper()->chooseImportOptions('Customers', 'Append Complex Data', 'Magento 1.7 format');
        //Step 5-6
        $importData = $this->importExportHelper()->import($report);
        //Verifying
        $this->assertEquals(
            'Checked rows: ' . count($report) . ', checked entities: '
                . $numberOfEntities . ', invalid rows: 0, total errors: 0',
            $importData['validation']['validation'][0],
            'Validation message is not correct'
        );
        $this->assertEquals(
            'File is valid! To start import process press "Import" button  Import',
            $importData['validation']['success'][0], 'Success message is not correct'
        );
    }

    /**
     * Required columns
     * Steps
     * 1. Go to System -> Import / Export -> Import
     * 2. Select Entity Type: Customers
     * 3. In Import Behavior dropdown field choose Append Complex Data parameter
     * 4. Choose file from precondition
     * 5. Click on Check Data
     * 6. Click on Import button
     * 7. Open Customers -> Manage Customers
     * 8. Open each of imported customers
     * Expected: 
     * After step 5
     * Verify that file is valid, the message 'File is valid!' is displayed
     * After step 6
     * Verify that import starting. The message 'Import successfully done.' is displayed
     * After step 7
     * Verify that imported customers display on customers grid
     * After step 8
     * Verify that all Customer information was imported
     *
     * @test
     * @dataProvider importWithRequiredColumnsData
     * @TestlinkId TL-MAGE-1167
     */
    public function importWithRequiredColumns($data)
    {
        //Set email for existing customer
        $data[0]['email'] = self::$_customerData['email'];
        //Steps 2-4
        $this->importExportHelper()->chooseImportOptions('Customers', 'Append Complex Data');
        //Steps 5-7
        $report = $this->importExportHelper()->import($data);
        //Verify import
        $this->assertArrayHasKey(
            'import',
            $report,
            'Import has been finished with issues:' . print_r($report, true) . print_r($data, true)
        );
        $this->assertArrayHasKey(
            'success',
            $report['import'],
            'Import has been finished with issues:' . print_r($report, true) . print_r($data, true)
        );
        //Check updated customer
        self::$_customerData['first_name'] = $data[0]['firstname'];
        self::$_customerData['last_name'] = $data[0]['lastname'];
        $this->navigate('manage_customers');
        $this->addParameter(
            'customer_first_last_name',
            self::$_customerData['first_name'] . ' ' . self::$_customerData['last_name']
        );
        $this->customerHelper()->openCustomer(array('email' => self::$_customerData['email']));
        $this->assertTrue(
            $this->verifyForm(self::$_customerData, 'account_information'),
            'Customer has not been updated'
        );
        //Check new customer
        $customerData = $this->loadDataSet(
            'Customers', 'generic_customer_account', array(
                'email' => $data[1]['email'],
                'first_name' => $data[1]['firstname'],
                'last_name' => $data[1]['lastname'],
            )
        );
        $this->navigate('manage_customers');
        $this->assertTrue(
            $this->customerHelper()->isCustomerPresentInGrid($customerData),
            'Customer has not been created'
        );
        $this->addParameter(
            'customer_first_last_name',
            $customerData['first_name'] . ' ' . $customerData['last_name']
        );
        $this->customerHelper()->openCustomer(
            array('email' => strtolower($customerData['email']))
        );
        $this->verifyForm($customerData, 'account_information');
    }

    public function importWithRequiredColumnsData()
    {
        return array(
            array(
                array($this->loadDataSet('ImportExport', 'generic_customer_csv', array(
                    'firstname' => 'New First Name',
                    'lastname' => 'New Last Name',
                    '_store' => '',
                    )
                ),
                $this->loadDataSet('ImportExport', 'generic_customer_csv', array('_store' => ''))
                )
            )
        );
    }

    /**
     * Columns for address
     * Verify that import of customer with address works correctly
     * Precondition: at least one customer exists, csv file contains two customers:
     * existing (with new customer information and address) and new one
     * Steps:
     * 1. Go to System -> Import/ Export -> Import
     * 2. In the drop-down "Entity Type" select Customers
     * 3. In the drop-down "Import Behavior" select "Append Complex Data"
     * 4. Choose file from precondition and click "Check Data" button
     * 5. Press "Import" button
     * 6. Go to Customer-> Manage Customers and open each of imported customers
     * Expected: customer information is updated for existing customer, new customer with address is added
     *
     * @test
     * @dataProvider columnsForAddressData
     * @TestlinkId TL-MAGE-1168
     */
    public function columnsForAddress($csv, $customerDataAfterImport, $addressDataAfterImport)
    {
        //Precondition: create new customer
        $this->navigate('manage_customers');
        $existingCustomerData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($existingCustomerData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Set correct email and entity id for csv and updated customer/address data
        foreach ($csv as $key => $value) {
            if (array_key_exists('email', $csv[$key]) && $csv[$key]['email'] == '<realEmail>') {
                $csv[$key]['email'] = $existingCustomerData['email'];
            }
            if (array_key_exists('email', $customerDataAfterImport[$key])
                && $customerDataAfterImport[$key]['email'] == '<realEmail>') {
                $customerDataAfterImport[$key]['email'] = $existingCustomerData['email'];
            }
        }
        //Step 1
        $this->navigate('import');
        //Steps 2-3
        $this->importExportHelper()->chooseImportOptions('Customers', 'Append Complex Data');
        //Steps 4-5
        $importData = $this->importExportHelper()->import($csv);
        //Verifying import
        $this->assertArrayNotHasKey('error', $importData['validation'],
            "File import has not been finished successfully" . print_r($importData, true));
        $this->assertArrayHasKey('import', $importData,
            "File import has not been finished successfully: " . print_r($importData, true));
        $this->assertArrayHasKey('success', $importData['import'],
            "File import has not been finished successfully" . print_r($importData, true));
        //Step 6
        foreach ($customerDataAfterImport as $key => $value) {
            $this->navigate('manage_customers');
            $this->assertTrue($this->customerHelper()->isCustomerPresentInGrid($customerDataAfterImport[$key]),
                'New customer has not been created');
            $this->addParameter('customer_first_last_name',
                $customerDataAfterImport[$key]['first_name'] . ' ' . $customerDataAfterImport[$key]['last_name']);
            $this->customerHelper()->openCustomer(array('email' => $customerDataAfterImport[$key]['email']));
            //Verifying customer data
            $this->assertTrue($this->verifyForm($customerDataAfterImport[$key], 'account_information'),
                'Customer information has not been updated');
            //Verifying address data
            $this->openTab('addresses');
            $this->assertNotEquals(0, $this->customerHelper()->isAddressPresent($addressDataAfterImport[$key]),
                'Customer address has not been added');
        }
    }

    public function columnsForAddressData()
    {
        //First csv row to update existing customer
        $csvCustomer[0] = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
                'email' => '<realEmail>',
                'firstname' => 'Robert',
                'lastname' => 'Barron',
                'middlename' => 'O.',
                'dob' => '14.01.1929 0:00',
                'gender' => 'Male',
                'taxvat' => '538-92-5393',
            )
        );
        $csvAddress[0] = array(
            '_address_city' => 'Kingsport',
            '_address_company' => 'Weingarten\'s',
            '_address_country_id' => 'US',
            '_address_fax' => '423-389-1069',
            '_address_firstname' => 'Linda',
            '_address_lastname' => 'Gilbert',
            '_address_middlename' => 'S.',
            '_address_postcode' => '37663',
            '_address_region' => 'Tennessee',
            '_address_street' => '1596 Public Works Drive',
            '_address_telephone' => '423-389-1069',
        );
        $csv[0] = array_merge($csvCustomer[0], $csvAddress[0]);
        $customerDataAfterImport[0] = $this->loadDataSet('Customers', 'generic_customer_account', array(
                'email' => '<realEmail>',
                'first_name' => 'Robert',
                'last_name' => 'Barron',
                'middle_name' => 'O.',
                'date_of_birth' => '1/14/1929',
                'gender' => 'Male',
                'tax_vat_number' => '538-92-5393',
                'password' => '',
            )
        );
        $addressDataAfterImport[0] = $this->loadDataSet('Customers', 'generic_address', array(
                'city' => 'Kingsport',
                'company' => 'Weingarten\'s',
                'country' => 'United States',
                'fax' => '423-389-1069',
                'first_name' => 'Linda',
                'last_name' => 'Gilbert',
                'middle_name' => 'S.',
                'zip_code' => '37663',
                'state' => 'Tennessee',
                'street_address_line_1' => '1596 Public Works Drive',
                'street_address_line_2' => '',
                'telephone' => '423-389-1069',
            )
        );

        //Second csv row to create new customer
        $csvCustomer[1] = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
                'email' => 'kennethmbolden@teleworm.us',
                'firstname' => 'Kenneth',
                'lastname' => 'Bolden',
                'middlename' => 'M.',
                'dob' => '28.02.1986 0:00',
                'gender' => 'Male',
                'taxvat' => '150-84-1427',
            )
        );
        $csvAddress[1] = array(
            '_address_city' => 'Dallas',
            '_address_company' => 'Flexus',
            '_address_country_id' => 'US',
            '_address_fax' => '972-677-4503',
            '_address_firstname' => 'Janice',
            '_address_lastname' => 'Padilla',
            '_address_middlename' => 'A.',
            '_address_postcode' => '75244',
            '_address_region' => 'Texas',
            '_address_street' => '628 Charla Lane',
            '_address_telephone' => '972-677-4503',
        );
        $csv[1] = array_merge($csvCustomer[1], $csvAddress[1]);
        $customerDataAfterImport[1] = $this->loadDataSet('Customers', 'generic_customer_account', array(
                'email' => 'kennethmbolden@teleworm.us',
                'first_name' => 'Kenneth',
                'last_name' => 'Bolden',
                'middle_name' => 'M.',
                'date_of_birth' => '2/28/1986',
                'gender' => 'Male',
                'tax_vat_number' => '150-84-1427',
                'password' => '',
            )
        );
        $addressDataAfterImport[1] = $this->loadDataSet('Customers', 'generic_address', array(
                'city' => 'Dallas',
                'company' => 'Flexus',
                'country' => 'United States',
                'fax' => '972-677-4503',
                'first_name' => 'Janice',
                'last_name' => 'Padilla',
                'middle_name' => 'A.',
                'zip_code' => '75244',
                'state' => 'Texas',
                'street_address_line_1' => '628 Charla Lane',
                'street_address_line_2' => '',
                'telephone' => '972-677-4503',
            )
        );

        return array(
            array($csv, $customerDataAfterImport, $addressDataAfterImport),
        );
    }
}