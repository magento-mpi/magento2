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
class Core_Mage_ImportExport_BackwardCompatibility_ImportTest extends Mage_Selenium_TestCase
{
    protected static $_customerData = array();
    protected static $_addressData = array();

    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->runMassAction('Delete', 'all', 'confirmation_for_massaction_delete');
        self::$_customerData = $this->loadDataSet('Customers', 'generic_customer_account');
        self::$_addressData = $this->loadDataSet('Customers', 'generic_address');
        $this->customerHelper()->createCustomer(self::$_customerData, self::$_addressData);
        $this->assertMessagePresent('success', 'success_saved_customer');
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
     * Has been excluded from functionality scope
     * Validation Result block
     * Verify that Validation Result block will be displayed after checking data of import file
     *
     * @test
     * @TestlinkId TL-MAGE-1108
     */
    public function validationResultBlock()
    {
        $this->markTestIncomplete('Customers option is not present while Export now');
        //Precondition
        $this->navigate('export');
        $this->importExportHelper()->chooseExportOptions('Customers');
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
        $this->importExportHelper()->chooseImportOptions('Customers', 'Append Complex Data');
        //Step 5-6
        $importData = $this->importExportHelper()->import($report);
        //Verifying
        $this->assertEquals(
            'Checked rows: ' . count($report) . ', checked entities: ' . $numberOfEntities
                . ', invalid rows: 0, total errors: 0',
            $importData['validation']['validation'][0],
            'Validation message is not correct'
        );
        $this->assertEquals('File is valid! To start import process press "Import" button  Import',
            $importData['validation']['success'][0], 'Success message is not correct');
    }

    /**
     * Required columns
     *
     * @test
     * @dataProvider importWithRequiredColumnsDataProvider
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
        $this->assertArrayHasKey('import', $report,
            'Import has been finished with issues:' . print_r($report, true) . print_r($data, true));
        $this->assertArrayHasKey('success', $report['import'],
            'Import has been finished with issues:' . print_r($report, true) . print_r($data, true));
        //Check updated customer
        self::$_customerData['first_name'] = $data[0]['firstname'];
        self::$_customerData['last_name'] = $data[0]['lastname'];
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => self::$_customerData['email']));
        $this->assertTrue($this->verifyForm(self::$_customerData, 'account_information'),
            'Customer has not been updated');
        //Check new customer
        $customerData = $this->loadDataSet('Customers', 'generic_customer_account', array(
            'email' => $data[1]['email'],
            'first_name' => $data[1]['firstname'],
            'last_name' => $data[1]['lastname']
        ));
        $this->navigate('manage_customers');
        $this->assertTrue($this->customerHelper()->isCustomerPresentInGrid($customerData),
            'Customer has not been created');
        $this->customerHelper()->openCustomer(array('email' => strtolower($customerData['email'])));
        $this->verifyForm($customerData, 'account_information');
    }

    public function importWithRequiredColumnsDataProvider()
    {
        return array(
            array(
                $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
                    'firstname' => 'New First Name',
                    'lastname' => 'New Last Name',
                    '_store' => ''
                )),
                $this->loadDataSet('ImportExport', 'generic_customer_csv', array('_store' => ''))
            )
        );
    }

    /**
     * Columns for address
     * Verify that import of customer with address works correctly
     *
     * @test
     * @dataProvider addressData
     * @TestlinkId TL-MAGE-1168
     */
    public function columnsForAddress($csv, $customerAfterImport, $addressAfterImport)
    {
        //Precondition: create new customer
        $this->navigate('manage_customers');
        $existingCustomerData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($existingCustomerData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Set correct email for csv and updated customer data
        foreach ($csv as $key => $value) {
            if (array_key_exists('email', $csv[$key]) && $csv[$key]['email'] == '<realEmail>') {
                $csv[$key]['email'] = $existingCustomerData['email'];
            }
            if (array_key_exists('email', $customerAfterImport[$key])
                && $customerAfterImport[$key]['email'] == '<realEmail>'
            ) {
                $customerAfterImport[$key]['email'] = $existingCustomerData['email'];
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
        foreach ($customerAfterImport as $key => $value) {
            $this->navigate('manage_customers');
            $this->assertTrue($this->customerHelper()->isCustomerPresentInGrid($value),
                'New customer has not been created');
            $this->customerHelper()->openCustomer(array('email' => $value['email']));
            //Verifying customer data
            $this->verifyForm($value, 'account_information', array('associate_to_website'));
            $this->assertEmptyVerificationErrors();
            //Verifying address data
            $this->openTab('addresses');
            $this->assertNotEquals(0, $this->customerHelper()->isAddressPresent($addressAfterImport[$key]),
                'Customer address has not been added');
        }
    }

    public function addressData()
    {
        //First csv row to update existing customer
        $csvCustomer[0] = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
            'email' => '<realEmail>',
            'firstname' => 'Robert',
            'lastname' => 'Barron',
            'middlename' => 'O.',
            'dob' => '14.01.1929 0:00',
            'gender' => 'Male',
            'taxvat' => '538-92-5393'
        ));
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
            '_address_telephone' => '423-389-1069'
        );
        $csv[0] = array_merge($csvCustomer[0], $csvAddress[0]);
        $customerAfterImport[0] = $this->loadDataSet('Customers', 'generic_customer_account', array(
            'email' => '<realEmail>',
            'first_name' => 'Robert',
            'last_name' => 'Barron',
            'middle_name' => 'O.',
            'date_of_birth' => '1/14/1929',
            'gender' => 'Male',
            'tax_vat_number' => '538-92-5393'
        ));
        $addressAfterImport[0] = $this->loadDataSet('Customers', 'generic_address', array(
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
            'telephone' => '423-389-1069'
        ));
        //Second csv row to create new customer
        $csvCustomer[1] = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
            'email' => 'kennethmbolden@teleworm.us',
            'firstname' => 'Kenneth',
            'lastname' => 'Bolden',
            'middlename' => 'M.',
            'dob' => '28.02.1986 0:00',
            'gender' => 'Male',
            'taxvat' => '150-84-1427'
        ));
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
            '_address_telephone' => '972-677-4503'
        );
        $csv[1] = array_merge($csvCustomer[1], $csvAddress[1]);
        $customerAfterImport[1] = $this->loadDataSet('Customers', 'generic_customer_account', array(
            'email' => 'kennethmbolden@teleworm.us',
            'first_name' => 'Kenneth',
            'last_name' => 'Bolden',
            'middle_name' => 'M.',
            'date_of_birth' => '2/28/1986',
            'gender' => 'Male',
            'tax_vat_number' => '150-84-1427'
        ));
        $addressAfterImport[1] = $this->loadDataSet('Customers', 'generic_address', array(
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
            'telephone' => '972-677-4503'
        ));

        return array(array($csv, $customerAfterImport, $addressAfterImport));
    }
}