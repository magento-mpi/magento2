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
 * Customer Import Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_ImportExport_Customer_ImportTest extends Mage_Selenium_TestCase
{
    protected static $_customerData = array();
    protected static $_addressData = array();

    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->runMassAction('Delete', 'all', 'confirmation_for_massaction_delete');
        self::$_customerData = $this->loadDataSet('Customers', 'generic_customer_account', array(
            'prefix' => 'Mr.',
            'date_of_birth' => '4/21/1963',
            'gender' => 'Male',
            'tax_vat_number' => '678-05-2568'
        ));
        self::$_addressData = $this->loadDataSet('Customers', 'generic_address');
        $this->customerHelper()->createCustomer(self::$_customerData, self::$_addressData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => self::$_customerData['email']));
        $this->openTab('addresses');
        self::$_addressData['address_id'] = $this->customerHelper()->isAddressPresent(self::$_addressData);
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/disable_secret_key');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('import');
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/enable_secret_key');
    }

    /**
     * Export Settings General View
     *
     * @test
     * @TestlinkId TL-MAGE-5615, TL-MAGE-5712
     */
    public function importSettingsGeneralView()
    {
        //Verifying Entity Type dropdown
        $entityTypes = $this->select($this->getControlElement('dropdown', 'entity_type'))->selectOptionLabels();
        $oldEntityTypes = array('Products', 'Customers');
        $newEntityTypes = $this->importExportHelper()->getCustomerEntityType();
        $expectedEntityTypes = array_merge(array('-- Please Select --', 'Products', 'Customers'), $newEntityTypes);
        $this->assertEquals($expectedEntityTypes, $entityTypes, 'Entity Type dropdown contains incorrect values');

        //Verifying Import Behavior dropdown
        $oldImportBehavior = array('Append Complex Data', 'Replace Existing Complex Data', 'Delete Entities');
        $newImportBehavior = array('Add/Update Complex Data', 'Delete Entities', 'Custom Action');

        foreach ($oldEntityTypes as $value) {
            $this->importExportHelper()->chooseImportOptions($value);
            $expectedBehavior = array_merge(array('-- Please Select --'), $oldImportBehavior);
            $actualBehavior = $this->select($this->getControlElement('dropdown', 'import_behavior'))
                ->selectOptionLabels();
            $this->assertEquals($expectedBehavior, $actualBehavior,
                'Import Behavior dropdown contains incorrect values');
            $this->assertTrue($this->controlIsVisible('field', 'file_to_import'), 'File to Import field is missing');
        }

        foreach ($newEntityTypes as $value) {
            $this->importExportHelper()->chooseImportOptions($value);
            $expectedBehavior = array_merge(array('-- Please Select --'), $newImportBehavior);
            $actualBehavior = $this->select($this->getControlElement('dropdown', 'import_behavior'))
                ->selectOptionLabels();
            $this->assertEquals($expectedBehavior, $actualBehavior,
                'Import Behavior dropdown contains incorrect values');
            $this->assertTrue($this->controlIsVisible('field', 'file_to_import'), 'File to Import field is missing');
        }
    }

    /**
     * Validation Result block
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
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '100'));
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '120'));
        }
        //export all customer files
        $this->navigate('export');
        $report = array();
        foreach ($customerTypes as $type) {
            $this->importExportHelper()->chooseExportOptions($type);
            $report[$type] = $this->importExportHelper()->export();
        }
        //Step 1
        $this->navigate('import');
        foreach ($customerTypes as $type) {
            //Step 2
            $this->importExportHelper()->chooseImportOptions($type, 'Add/Update Complex Data');
            //Steps 3-4
            $importData = $this->importExportHelper()->import($report[$type]);
            //Verifying
            $this->assertEquals(
                'Checked rows: ' . count($report[$type]) . ', checked entities: ' . count($report[$type])
                    . ', invalid rows: 0, total errors: 0',
                $importData['validation']['validation'][0],
                'Validation message is not correct'
            );
            $this->assertEquals(
                'File is valid! To start import process press "Import" button  Import',
                $importData['validation']['success'][0],
                'Success message is not correct'
            );
        }
    }

    /**
     * Required columns
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
        $this->importExportHelper()->chooseImportOptions('Customers Main File', 'Add/Update Complex Data');
        //Generated CSV data
        $customerCsv[0] = $this->loadDataSet('ImportExport', 'generic_customer_csv',
            array('email' => $customerData['email'], 'group_id' => '3'));
        $customerCsv[1] = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
            'email' => 'test_admin_' . $this->generate('string', 5) . '@unknown-domain.com',
            'firstname' => 'first_' . $this->generate('string', 10),
            'lastname' => 'last_' . $this->generate('string', 10),
            'group_id' => '3'
        ));
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
        $this->customerHelper()->openCustomer(array('email' => strtolower($data[0]['email'])));
        //Verify customer account
        $customerData['group'] = 'Retailer';
        $customerData['first_name'] = $customerCsv[0]['firstname'];
        $customerData['last_name'] = $customerCsv[0]['lastname'];
        $this->verifyForm($customerData, 'account_information', array('associate_to_website'));
        $this->assertEmptyVerificationErrors();
        //Verify customer account
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => strtolower($data[1]['email'])));
        $customerData['group'] = 'Retailer';
        $customerData['email'] = strtolower($customerCsv[1]['email']);
        $customerData['first_name'] = $customerCsv[1]['firstname'];
        $customerData['last_name'] = $customerCsv[1]['lastname'];
        $this->verifyForm($customerData, 'account_information', array('associate_to_website'));
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Not required columns
     *
     * @test
     * @dataProvider notRequiredColumnsMainData
     * @TestlinkId TL-MAGE-5622, TL-MAGE-5623
     */
    public function notRequiredColumnsMain($csvData, $updatedData)
    {
        //Set correct email and entity id for csv and updated customer/address data
        foreach ($csvData as $key => $value) {
            if (array_key_exists('email', $value) && $value['email'] == '<realEmail>') {
                $csvData[$key]['email'] = self::$_customerData['email'];
            }
        }
        foreach ($updatedData as $key => $value) {
            if (array_key_exists('email', $value) && $value['email'] == '<realEmail>') {
                $updatedData[$key]['email'] = self::$_customerData['email'];
            }
        }
        //Step 1
        $this->navigate('import');
        //Steps 2-3
        $this->importExportHelper()->chooseImportOptions('Customers Main File', 'Add/Update Complex Data');
        //Steps 4-5
        $importData = $this->importExportHelper()->import($csvData);
        //Verifying import
        $this->assertArrayHasKey('import', $importData,
            "File import has not been finished successfully: " . print_r($importData, true));
        $this->assertArrayHasKey('success', $importData['import'],
            "File import has not been finished successfully" . print_r($importData, true));
        //Step 6
        foreach ($updatedData as $key => $value) {
            $this->navigate('manage_customers');
            $this->assertTrue($this->customerHelper()->isCustomerPresentInGrid($value),
                'New customer has not been created');
            $this->customerHelper()->openCustomer(array('email' => $value['email']));
            //Verifying customer data
            $this->verifyForm($value, 'account_information', array('associate_to_website'));
            $this->assertEmptyVerificationErrors();
            //Update original customer data
            if ($key == 1) {
                self::$_customerData = $value;
            }
        }
    }

    public function notRequiredColumnsMainData()
    {
        $csvMain[0] = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
            'firstname' => 'First Name',
            'lastname' => 'Last Name',
            'middlename' => 'Middle Name',
            'dob' => '22.01.1986 0:00',
            'prefix' => 'Mr.',
            'gender' => 'Male',
            'taxvat' => '483-31-8400'
        ));
        $customerUpdatedData[0] = $this->loadDataSet('Customers', 'generic_customer_account', array(
            'email' => $csvMain[0]['email'],
            'middle_name' => 'Middle Name',
            'date_of_birth' => '1/22/1986',
            'prefix' => 'Mr.',
            'gender' => 'Male',
            'tax_vat_number' => '483-31-8400',
        ));
        $csvMain[1] = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
            'email' => '<realEmail>',
            'firstname' => 'New First Name',
            'lastname' => 'New Last Name',
            'middlename' => 'New Middle Name',
            'prefix' => 'Ms.',
            'gender' => 'Female',
            'dob' => '01.05.1964 0:00',
            'taxvat' => '501-92-8747'
        ));
        $customerUpdatedData[1] = $this->loadDataSet('Customers', 'generic_customer_account', array(
            'email' => '<realEmail>',
            'first_name' => 'New First Name',
            'last_name' => 'New Last Name',
            'middle_name' => 'New Middle Name',
            'prefix' => 'Ms.',
            'gender' => 'Female',
            'date_of_birth' => '5/1/1964',
            'tax_vat_number' => '501-92-8747',
        ));

        $mainCsvRows = array($csvMain[0], $csvMain[1]);
        $updatedCustomerData = array($customerUpdatedData[0], $customerUpdatedData[1]);

        return array(
            array($mainCsvRows, $updatedCustomerData),
        );
    }

    /**
     * Not required columns
     *
     * @test
     * @dataProvider notRequiredColumnsAddressData
     * @TestlinkId TL-MAGE-5622, TL-MAGE-5623
     */
    public function notRequiredColumnsAddress($csvData, $updatedData)
    {
        //Set correct email and entity id for csv and updated customer/address data
        foreach ($csvData as $key => $value) {
            if (array_key_exists('_email', $value) && $value['_email'] == '<realEmail>') {
                $csvData[$key]['_email'] = self::$_customerData['email'];
            }
            if (array_key_exists('_entity_id', $value) && $value['_entity_id'] == '<realEntityID>') {
                $csvData[$key]['_entity_id'] = self::$_addressData['address_id'];
            }
        }
        //Step 1
        $this->navigate('import');
        //Steps 2-3
        $this->importExportHelper()->chooseImportOptions('Customer Addresses', 'Add/Update Complex Data');
        //Steps 4-5
        $importData = $this->importExportHelper()->import($csvData);
        //Verifying import
        $this->assertArrayHasKey('import', $importData,
            "File import has not been finished successfully: " . print_r($importData, true));
        $this->assertArrayHasKey('success', $importData['import'],
            "File import has not been finished successfully" . print_r($importData, true));
        //Step 6
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => self::$_customerData['email']));
        $this->openTab('addresses');
        foreach ($updatedData as $value) {
            $this->assertNotEquals(0, $this->customerHelper()->isAddressPresent($value),
                'Customer address has not been added/updated');
        }
    }

    public function notRequiredColumnsAddressData()
    {
        $csvAddress[0] = $this->loadDataSet('ImportExport', 'generic_address_csv', array('_email' => '<realEmail>',));
        $addressUpdatedData[0] = $this->loadDataSet('Customers', 'generic_address', array(
            'prefix' => 'Mr.',
            'first_name' => 'Alvin',
            'middle_name' => 'C.',
            'last_name' => 'Plyler',
            'company' => 'Earl Abel\'s',
            'street_address_line_1' => '539 Russell Street',
            'street_address_line_2' => '',
            'city' => 'New York',
            'state' => 'Massachusetts',
            'zip_code' => '57428',
            'telephone' => '978-875-6394',
            'fax' => '978-875-6394'
        ));
        $csvAddress[1] = $this->loadDataSet('ImportExport', 'generic_address_csv', array(
            '_email' => '<realEmail>',
            '_entity_id' => '<realEntityID>',
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
            'telephone' => '609-504-6350'
        ));
        $addressUpdatedData[1] = $this->loadDataSet('Customers', 'generic_address', array(
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
            'fax' => '609-504-6350'
        ));

        $addressCsvRows = array($csvAddress[0], $csvAddress[1]);
        $updatedAddressData = array($addressUpdatedData[0], $addressUpdatedData[1]);

        return array(
            array($addressCsvRows, $updatedAddressData),
        );
    }

    /**
     * Partial Import (Main file)
     * Verify that if import file has some invalid data, then import will be finished partially
     * Precondition: two csv files prepared. First one contains two rows: valid customer data (new customer),
     * invalid customer data (non existing website id). Second one contains two rows: valid customer data (new
     * customer), valid customer data (email and website id is the same as in first row)
     *
     * @test
     * @dataProvider partialImportData
     * @TestlinkId TL-MAGE-5635
     */
    public function partialImport($csvData, $newCustomerData, $validation)
    {
        //Set correct email for csv data
        foreach ($csvData as $key => $value) {
            if (array_key_exists('email', $value) && $value['email'] == '<realEmail>') {
                $csvData[$key]['email'] = self::$_customerData['email'];
            }
        }
        //Steps 2-3
        $this->importExportHelper()->chooseImportOptions('Customers Main File', 'Add/Update Complex Data');
        //Steps 4-5
        $importData = $this->importExportHelper()->import($csvData);
        //Verifying import
        $this->assertEquals($validation, $importData, 'Import has been finished with issues');
        //Step 6
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => $newCustomerData['email']));
        //Verifying that new customer is created
        $this->verifyForm($newCustomerData, 'account_information', array('associate_to_website'));
        $this->assertEmptyVerificationErrors();
    }

    public function partialImportData()
    {
        $csv[0][0] = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
            'firstname' => 'Sean',
            'lastname' => 'Morgan',
            'middlename' => 'M.',
            'gender' => 'Male'
        ));
        $newCustomerData[0] = $this->loadDataSet('Customers', 'generic_customer_account', array(
            'email' => $csv[0][0]['email'],
            'first_name' => 'Sean',
            'last_name' => 'Morgan',
            'middle_name' => 'M.',
            'gender' => 'Male'
        ));
        $csv[0][1] = $this->loadDataSet('ImportExport', 'generic_customer_csv',
            array('email' => '<realEmail>', '_website' => 'invalid'));
        $csvFile[0] = array($csv[0][0], $csv[0][1]);
        $message[0] = array(
            'validation' => array(
                'error' => array("Invalid value in website column in rows: 2"),
                'validation' => array(
                    "Please fix errors and re-upload file or simply press \"Import\" button to skip rows with errors  Import",
                    "Checked rows: 2, checked entities: 2, invalid rows: 1, total errors: 1",
                )
            ),
            'import' => array(
                'success' => array('Import successfully done'),
            ),
        );

        $csv[1][0] = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
            'firstname' => 'Ann',
            'lastname' => 'Gordon',
            'middlename' => 'G.',
            'gender' => 'Female'
        ));
        $newCustomerData[1] = $this->loadDataSet('Customers', 'generic_customer_account', array(
            'email' => $csv[1][0]['email'],
            'first_name' => 'Ann',
            'last_name' => 'Gordon',
            'middle_name' => 'G.',
            'gender' => 'Female'
        ));
        $csv[1][1] = $csv[1][0];
        $csvFile[1] = array($csv[1][0], $csv[1][1]);
        $message[1] = array(
            'validation' => array(
                'error' => array("E-mail is duplicated in import file in rows: 2"),
                'validation' => array(
                    "Please fix errors and re-upload file or simply press \"Import\" button to skip rows with errors  Import",
                    "Checked rows: 2, checked entities: 2, invalid rows: 1, total errors: 1",
                )
            ),
            'import' => array(
                'success' => array('Import successfully done'),
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
        $this->importExportHelper()->import($data);
    }

    public function importData()
    {
        return array(
            array(array(
                $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
                    'email' => 'sdfsdf@qweqwe.cc',
                    '_website' => 'base',
                    '_store' => 'admin',
                    'confirmation' => '',
                    'created_at' => '01.06.2012 14:35',
                    'created_in' => 'Admin',
                    'default_billing' => '',
                    'default_shipping' => '',
                    'disable_auto_group_change' => '0',
                    'dob' => '',
                    'firstname' => 'sdfsdfsd',
                    'gender' => '',
                    'group_id' => '1',
                    'lastname' => 'sdfsdfs',
                    'middlename' => '',
                    'password_hash' => '48927b9ee38afb672504488a45c0719140769c24c10e5ba34d203ce5a9c15b27:2y',
                    'prefix' => '',
                    'rp_token' => '',
                    'rp_token_created_at' => '',
                    'store_id' => '0',
                    'suffix' => '',
                    'taxvat' => '',
                    'website_id' => '0',
                )))
            )
        );
    }
}
