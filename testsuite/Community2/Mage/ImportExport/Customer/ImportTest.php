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
 * Customer Import Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_ImportExport_Import_CustomerTest extends Mage_Selenium_TestCase
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
        self::$_customerData = $this->loadDataSet('Customers', 'generic_customer_account',
            array('prefix'         => 'Mr.', 'date_of_birth' => '4/21/1963', 'gender' => 'Male',
                  'tax_vat_number' => '678-05-2568',));
        self::$_addressData = $this->loadDataSet('Customers', 'generic_address');
        $this->customerHelper()->createCustomer(self::$_customerData, self::$_addressData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->addParameter('customer_first_last_name',
            self::$_customerData['first_name'] . ' ' . self::$_customerData['last_name']);
        $this->customerHelper()->openCustomer(array('email' => self::$_customerData['email']));
        self::$_addressData['address_id'] = $this->customerHelper()->isAddressPresent(self::$_addressData);
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
     * Export Settings General View
     * Steps
     * 1. Go to System -> Import/ Export -> Import
     * 2. Verify that Entity Type dropdown contains correct values
     * 3. Verify that Import Behavior dropdown contains correct values according to selected entity type
     * Expected: dropdowns contain correct values
     *
     * @test
     * @TestlinkId TL-MAGE-5615, TL-MAGE-5712
     */
    public function importSettingsGeneralView()
    {
        //Verifying Entity Type dropdown
        $entityTypes =
            $this->getElementsByXpath($this->_getControlXpath('dropdown', 'entity_type') . '/option', 'text');
        $oldEntityTypeValues = array('Products', 'Customers');
        $newEntityTypeValues = $this->importExportHelper()->getCustomerEntityType();
        $expectedEntityTypeValues = array_merge(array('-- Please Select --', 'Products', 'Customers'),
            $newEntityTypeValues);
        $this->assertEquals($expectedEntityTypeValues, $entityTypes, 'Entity Type dropdown contains incorrect values');

        //Verifying Import Behavior dropdown
        $oldImportBehavior = array('Append Complex Data', 'Replace Existing Complex Data', 'Delete Entities');
        $newImportBehavior = array('Add/Update Complex Data', 'Delete Entities', 'Custom Action');

        foreach ($oldEntityTypeValues as $value) {
            $this->importExportHelper()->chooseImportOptions($value);
            $expectedImportBehavior = array_merge(array('-- Please Select --'), $oldImportBehavior);
            $actualImportBehavior =
                $this->getElementsByXpath($this->_getControlXpath('dropdown', 'import_behavior') . '/option', 'text');
            $this->assertEquals($expectedImportBehavior, $actualImportBehavior,
                'Import Behavior dropdown contains incorrect values');
            $this->assertTrue($this->controlIsVisible('field', 'file_to_import'), 'File to Import field is missing');
        }

        foreach ($newEntityTypeValues as $value) {
            $this->importExportHelper()->chooseImportOptions($value);
            $expectedImportBehavior = array_merge(array('-- Please Select --'), $newImportBehavior);
            $actualImportBehavior =
                $this->getElementsByXpath($this->_getControlXpath('dropdown', 'import_behavior') . '/option', 'text');
            $this->assertEquals($expectedImportBehavior, $actualImportBehavior,
                'Import Behavior dropdown contains incorrect values');
            $this->assertTrue($this->controlIsVisible('field', 'file_to_import'), 'File to Import field is missing');
        }
    }

    /**
     * Validation Result block
     * Verify that Validation Result block will be displayed after checking data of import customer files
     * Precondition: at least one customer exists,
     * Customer Main, Address, Finance files must be generated after export
     * Steps
     * 1. Go to System -> Import/ Export -> Import
     * 2. Select Customers Entity Type: Customer Main File/Customer Addresses/Customer Finances
     * 3. Select file to import
     * 4. Click "Check Data" button.
     * Expected: validation and success messages are correct
     *
     * @test
     * @TestlinkId TL-MAGE-5618, TL-MAGE-5619, TL-MAGE-5620
     */
    public function validationResultBlock()
    {
        //Precondition: create customer, add address
        $this->navigate('manage_customers');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $addressData = $this->loadDataSet('Customers', 'generic_address');
        $this->customerHelper()->createCustomer($userData, $addressData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //add store credit and reward points (for EE)
        $customerTypes = $this->importExportHelper()->getCustomerEntityType();
        if (in_array('Customer Finances', $customerTypes)) {
            $this->addParameter('customer_first_last_name', $userData['first_name'] . ' ' . $userData['last_name']);
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '100'));
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '120'));
        }
        //export all customer files
        $this->navigate('export');
        $report = array();
        foreach ($customerTypes as $customerType) {
            $this->importExportHelper()->chooseExportOptions($customerType);
            $report[$customerType] = $this->importExportHelper()->export();
        }
        //Step 1
        $this->navigate('import');
        foreach ($customerTypes as $customerType) {
            //Step 2
            $this->importExportHelper()->chooseImportOptions($customerType, 'Add/Update Complex Data');
            //Steps 3-4
            $importData = $this->importExportHelper()->import($report[$customerType]);
            //Verifying
            $this->assertEquals('Checked rows: ' . count($report[$customerType]) . ', checked entities: '
                                . count($report[$customerType]) . ', invalid rows: 0, total errors: 0',
                $importData['validation']['validation'][0], 'Validation message is not correct');
            $this->assertEquals('File is valid! To start import process press "Import" button  Import',
                $importData['validation']['success'][0], 'Success message is not correct');
        }
    }

    /**
     * Required columns
     * Steps
     * 1. Go to System -> Import / Export -> Import
     * 2. Select Entity Type: Customers Main File
     * 3. Select Import Behavior: Add/Update Complex Data
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
     * @TestlinkId TL-MAGE-5621
     */
    public function importWithRequiredColumns()
    {
        //Precondition: create new customer
        $this->navigate('manage_customers');
        // 0.1. create customer
        $customerData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($customerData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('import');
        //Step 1
        $this->importExportHelper()
            ->chooseImportOptions('Customers Main File', 'Add/Update Complex Data');
        //Generated CSV data
        $customerCsv[0] = $this->loadDataSet('ImportExport', 'generic_customer_csv',
            array('email' => $customerData['email'], 'group_id' => '3'));
        $customerCsv[1] = $this->loadDataSet('ImportExport', 'generic_customer_csv',
            array('email'     => 'test_admin_' . $this->generate('string', 5) . '@unknown-domain.com',
                  'firstname' => 'first_' . $this->generate('string', 10),
                  'lastname'  => 'last_' . $this->generate('string', 10), 'group_id' => '3'));
        //Build CSV array
        $data = array($customerCsv[0], $customerCsv[1]);
        //Import file with default flow
        $report = $this->importExportHelper()->import($data);
        //Check import
        $this->assertArrayHasKey('import', $report,
            'Import has been finished with issues:' . print_r($report, true) . print_r($data, true));
        $this->assertArrayHasKey('success', $report['import'],
            'Import has been finished with issues:' . print_r($report, true) . print_r($data, true));
        //Check customers
        $this->navigate('manage_customers');
        //Check updated customer
        $this->addParameter('customer_first_last_name', $data[0]['firstname'] . ' ' . $data[0]['lastname']);
        $this->customerHelper()->openCustomer(array('email' => strtolower($data[0]['email'])));
        //Verify customer account
        $customerData['group'] = 'Retailer';
        $customerData['first_name'] = $customerCsv[0]['firstname'];
        $customerData['last_name'] = $customerCsv[0]['lastname'];
        $this->assertTrue($this->verifyForm($customerData, 'account_information'),
            'Existent customer has not been updated');
        //Verify customer account
        $this->navigate('manage_customers');
        $this->addParameter('customer_first_last_name', $data[1]['firstname'] . ' ' . $data[1]['lastname']);
        $this->customerHelper()->openCustomer(array('email' => strtolower($data[1]['email'])));
        $customerData['group'] = 'Retailer';
        $customerData['email'] = strtolower($customerCsv[1]['email']);
        $customerData['first_name'] = $customerCsv[1]['firstname'];
        $customerData['last_name'] = $customerCsv[1]['lastname'];
        $this->assertTrue($this->verifyForm($customerData, 'account_information'), 'New customer has not been created');
    }

    /**
     * Not required columns
     * Verify that all  not required values are imported.
     * Precondition: at least one customer exists,
     * csv file contains two customers: existing (with new attribute values) and new one
     * Steps
     * 1. Go to System -> Import/ Export -> Import
     * 2. In the drop-down "Entity Type" select Customers Main File/Customer Addresses
     * 3. In the drop-down "Import Behavior" select "Add/Update Complex Data"
     * 4. Choose file from precondition and click "Check Data" button
     * 5. Press "Import" button
     * 6. Go to Customer-> Manage Customers and open each of imported customers
     * Expected: values of not required attributes is updated for existing customer,
     * new customer is added with proper values of not required attributes
     *
     * @test
     * @dataProvider notRequiredColumnsData
     * @TestlinkId TL-MAGE-5622, TL-MAGE-5623
     */
    public function notRequiredColumns($customerType, $csvData, $updatedData)
    {
        //Set correct email and entity id for csv and updated customer/address data
        foreach ($csvData as $key => $value) {
            if (array_key_exists('email', $csvData[$key]) && $csvData[$key]['email'] == '<realEmail>') {
                $csvData[$key]['email'] = self::$_customerData['email'];
            }
            if (array_key_exists('_email', $csvData[$key]) && $csvData[$key]['_email'] == '<realEmail>') {
                $csvData[$key]['_email'] = self::$_customerData['email'];
            }
            if (array_key_exists('_entity_id', $csvData[$key]) && $csvData[$key]['_entity_id'] == '<realEntityID>') {
                $csvData[$key]['_entity_id'] = self::$_addressData['address_id'];
            }
        }
        foreach ($updatedData as $key => $value) {
            if (array_key_exists('email', $updatedData[$key]) && $updatedData[$key]['email'] == '<realEmail>') {
                $updatedData[$key]['email'] = self::$_customerData['email'];
            }
        }
        //Step 1
        $this->navigate('import');
        //Steps 2-3
        $this->importExportHelper()->chooseImportOptions($customerType, 'Add/Update Complex Data');
        //Steps 4-5
        $importData = $this->importExportHelper()->import($csvData);
        //Verifying import
        $this->assertArrayHasKey('import', $importData,
            "$customerType file import has not been finished successfully: " . print_r($importData, true));
        $this->assertArrayHasKey('success', $importData['import'],
            "$customerType file import has not been finished successfully" . print_r($importData, true));
        //Step 6
        if ($customerType == 'Customers Main File') {
            foreach ($updatedData as $key => $value) {
                $this->navigate('manage_customers');
                $this->assertTrue($this->customerHelper()->isCustomerPresentInGrid($updatedData[$key]),
                    'New customer has not been created');
                $this->addParameter('customer_first_last_name',
                    $updatedData[$key]['first_name'] . ' ' . $updatedData[$key]['last_name']);
                $this->customerHelper()->openCustomer(array('email' => $updatedData[$key]['email']));
                //Verifying customer data
                $this->assertTrue($this->verifyForm($updatedData[$key], 'account_information'),
                    'Customer has not been updated');
                //Update original customer data
                if ($key == 1) {
                    self::$_customerData = $updatedData[$key];
                }
            }
        }
        //Verifying address data
        if ($customerType == 'Customer Addresses') {
            $this->navigate('manage_customers');
            $this->addParameter('customer_first_last_name',
                self::$_customerData['first_name'] . ' ' . self::$_customerData['last_name']);
            $this->customerHelper()->openCustomer(array('email' => self::$_customerData['email']));
            $this->openTab('addresses');
            foreach ($updatedData as $key => $value) {
                $this->assertNotEquals(0, $this->customerHelper()->isAddressPresent($updatedData[$key]),
                    'Customer address has not been added/updated');
            }
        }
    }

    public function notRequiredColumnsData()
    {
        $csvMain[0] = $this->loadDataSet('ImportExport', 'generic_customer_csv',
            array('firstname' => 'First Name', 'lastname' => 'Last Name', 'middlename' => 'Middle Name',
                  'dob'       => '22.01.1986 0:00', 'prefix' => 'Mr.', 'gender' => 'Male', 'taxvat' => '483-31-8400',));
        $customerUpdatedData[0] = $this->loadDataSet('Customers', 'generic_customer_account',
            array('email'  => $csvMain[0]['email'], 'middle_name' => 'Middle Name', 'date_of_birth' => '1/22/1986',
                  'prefix' => 'Mr.', 'gender' => 'Male', 'tax_vat_number' => '483-31-8400', 'password' => '',));
        $csvMain[1] = $this->loadDataSet('ImportExport', 'generic_customer_csv',
            array('email'      => '<realEmail>', 'firstname' => 'New First Name', 'lastname' => 'New Last Name',
                  'middlename' => 'New Middle Name', 'prefix' => 'Ms.', 'gender' => 'Female',
                  'dob'        => '01.05.1964 0:00', 'taxvat' => '501-92-8747',));
        $customerUpdatedData[1] = $this->loadDataSet('Customers', 'generic_customer_account',
            array('email'         => '<realEmail>', 'first_name' => 'New First Name', 'last_name' => 'New Last Name',
                  'middle_name'   => 'New Middle Name', 'prefix' => 'Ms.', 'gender' => 'Female',
                  'date_of_birth' => '5/1/1964', 'tax_vat_number' => '501-92-8747', 'password' => '',));

        $mainCsvRows = array($csvMain[0], $csvMain[1]);
        $updatedCustomerData = array($customerUpdatedData[0], $customerUpdatedData[1]);

        $csvAddress[0] = $this->loadDataSet('ImportExport', 'generic_address_csv', array('_email' => '<realEmail>',));
        $addressUpdatedData[0] = $this->loadDataSet('Customers', 'generic_address',
            array('prefix'                => 'Mr.', 'first_name' => 'Alvin', 'middle_name' => 'C.',
                  'last_name'             => 'Plyler', 'company' => 'Earl Abel\'s',
                  'street_address_line_1' => '539 Russell Street', 'street_address_line_2' => '', 'city' => 'New York',
                  'state'                 => 'Massachusetts', 'zip_code' => '57428', 'telephone' => '978-875-6394',
                  'fax'                   => '978-875-6394',));
        $csvAddress[1] = $this->loadDataSet('ImportExport', 'generic_address_csv',
            array('_email'   => '<realEmail>', '_entity_id' => '<realEntityID>', 'city' => 'Camden',
                  'company'  => 'Team Electronics', 'fax' => '609-504-6350', 'firstname' => 'William',
                  'lastname' => 'Holler', 'middlename' => 'E.', 'postcode' => '08102', 'prefix' => '',
                  'region'   => 'New Jersey', 'street' => '3186 Lincoln Street', 'telephone' => '609-504-6350',));
        $addressUpdatedData[1] = $this->loadDataSet('Customers', 'generic_address',
            array('first_name'            => 'William', 'middle_name' => 'E.', 'last_name' => 'Holler',
                  'company'               => 'Team Electronics', 'street_address_line_1' => '3186 Lincoln Street',
                  'street_address_line_2' => '', 'city' => 'Camden', 'state' => 'New Jersey', 'zip_code' => '08102',
                  'telephone'             => '609-504-6350', 'fax' => '609-504-6350',));

        $addressCsvRows = array($csvAddress[0], $csvAddress[1]);
        $updatedAddressData = array($addressUpdatedData[0], $addressUpdatedData[1]);

        return array(
            array('Customers Main File', $mainCsvRows, $updatedCustomerData),
            array('Customer Addresses', $addressCsvRows, $updatedAddressData),
        );
    }

    /**
     * Partial Import (Main file)
     * Verify that if import file has some invalid data, then import will be finished partially
     * Precondition: two csv files prepared. First one contains two rows: valid customer data (new customer),
     * invalid customer data (non existing website id). Second one contains two rows: valid customer data (new
     * customer), valid customer data (email and website id is the same as in first row)
     * Steps
     * 1. Go to System -> Import/ Export -> Import
     * 2. In the drop-down "Entity Type" select "Customers Main File"
     * 3. Select "Add/Update Complex Data" Import Behavior
     * 4. Choose first file from precondition, click "Check Data" button, Press "Import" button
     * Expected: messages "Invalid value in Website column (website does not exists?) in rows: 2", "Please fix
     * errors and re-upload file or simply press "Import" button to skip rows with errors" and "Checked rows: 2,
     * checked entities: 2, invalid rows: 1, total errors: 1"
     * are displayed
     * 5. Choose second file from precondition, click "Check Data" button, Press "Import" button
     * Expected: messages "E-mail is duplicated in import file in rows: 2" and "Please fix errors and re-upload
     * file or simply press "Import" button to skip rows with errors", "Checked rows: 2, checked entities: 2, invalid
     * rows: 1, total errors: 1" are displayed
     * 6. Open imported customers
     * Expected: valid data information was imported correctly
     *
     * @test
     * @dataProvider partialImportData
     * @TestlinkId TL-MAGE-5635
     */
    public function partialImport($csvData, $newCustomerData, $validation)
    {
        //Set correct email for csv data
        foreach ($csvData as $key => $value) {
            if (array_key_exists('email', $csvData[$key]) && $csvData[$key]['email'] == '<realEmail>') {
                $csvData[$key]['email'] = self::$_customerData['email'];
            }
        }
        //Steps 2-3
        $this->importExportHelper()
            ->chooseImportOptions('Customers Main File', 'Add/Update Complex Data');
        //Steps 4-5
        $importData = $this->importExportHelper()->import($csvData);
        //Verifying import
        $this->assertEquals($validation, $importData, 'Import has been finished with issues');
        //Step 6
        $this->navigate('manage_customers');
        $this->addParameter('customer_first_last_name',
            $newCustomerData['first_name'] . ' ' . $newCustomerData['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $newCustomerData['email']));
        //Verifying that new customer is created
        $this->assertTrue($this->verifyForm($newCustomerData, 'account_information'),
            'New customer has not been created');
    }

    public function partialImportData()
    {
        $csv[0][0] = $this->loadDataSet('ImportExport', 'generic_customer_csv',
            array('firstname' => 'Sean', 'lastname' => 'Morgan', 'middlename' => 'M.', 'gender' => 'Male',));
        $newCustomerData[0] = $this->loadDataSet('Customers', 'generic_customer_account',
            array('email'       => $csv[0][0]['email'], 'first_name' => 'Sean', 'last_name' => 'Morgan',
                  'middle_name' => 'M.', 'gender' => 'Male',));
        $csv[0][1] = $this->loadDataSet('ImportExport', 'generic_customer_csv',
            array('email' => '<realEmail>', '_website' => 'invalid',));

        $csvFile[0] = array($csv[0][0], $csv[0][1]);

        $message[0] = array('validation' => array(
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

        $csv[1][0] = $this->loadDataSet('ImportExport', 'generic_customer_csv',
            array('firstname' => 'Ann', 'lastname' => 'Gordon', 'middlename' => 'G.', 'gender' => 'Female',));
        $newCustomerData[1] = $this->loadDataSet('Customers', 'generic_customer_account',
            array('email'       => $csv[1][0]['email'], 'first_name' => 'Ann', 'last_name' => 'Gordon',
                  'middle_name' => 'G.', 'gender' => 'Female',));
        $csv[1][1] = $csv[1][0];

        $csvFile[1] = array($csv[1][0], $csv[1][1]);

        $message[1] = array('validation' => array(
            'error' => array("E-mail is duplicated in import file in rows: 2"),
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
            array($csvFile[0], $newCustomerData[0], $message[0]),
            array($csvFile[1], $newCustomerData[1], $message[1]),
        );

    }

    /**
     * @dataProvider importData
     * @test
     */
    public function simpleImport($data)
    {
        //Step 1
        $this->importExportHelper()->chooseImportOptions('Customers', 'Append Complex Data');
        $report = $this->importExportHelper()->import($data);
    }

    public function importData()
    {
        return array(
            array(array(
                $this->loadDataSet('ImportExport', 'generic_customer_csv',
                    array(
                        'email'           => 'sdfsdf@qweqwe.cc', '_website' => 'base', '_store' => 'admin',
                        'confirmation'    => '', 'created_at' => '01.06.2012 14:35', 'created_in' => 'Admin',
                        'default_billing' => '', 'default_shipping' => '', 'disable_auto_group_change' => '0',
                        'dob'             => '', 'firstname' => 'sdfsdfsd', 'gender' => '', 'group_id' => '1',
                        'lastname'        => 'sdfsdfs', 'middlename' => '',
                        'password_hash'   => '48927b9ee38afb672504488a45c0719140769c24c10e5ba34d203ce5a9c15b27:2y',
                        'prefix'          => '', 'rp_token' => '', 'rp_token_created_at' => '', 'store_id' => '0',
                        'suffix'          => '', 'taxvat' => '', 'website_id' => '0', 'password' => '')
                    )
                )
            )
        );
    }
}