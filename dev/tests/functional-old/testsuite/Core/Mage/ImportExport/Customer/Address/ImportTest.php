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
class Core_Mage_ImportExport_Customer_Address_ImportTest extends Mage_Selenium_TestCase
{
    protected static $_customerData = array();

    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->runMassAction('Delete', 'all', 'confirmation_for_massaction_delete');
        self::$_customerData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer(self::$_customerData);
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
     * Required columns
     *
     * @test
     * @TestlinkId TL-MAGE-5624
     */
    public function importWithRequiredColumns()
    {
        //Precondition: create 2 new customers
        $this->navigate('manage_customers');
        //Create customers with address
        $this->navigate('manage_customers');
        $userWithAddressData = $this->loadDataSet('Customers', 'generic_customer_account');
        $existingAddressData = $this->loadDataSet('Customers', 'generic_address');
        $this->customerHelper()->createCustomer($userWithAddressData, $existingAddressData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Get existing address id to use in csv file
        $this->customerHelper()->openCustomer(array('email' => $userWithAddressData['email']));
        $this->openTab('addresses');
        $addressIdExisting = $this->customerHelper()->isAddressPresent($existingAddressData);
        $this->navigate('manage_customers');
        //Create customers without address
        $userWithoutAddress = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userWithoutAddress);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Step 1
        $this->navigate('import');
        //Steps 2-3
        $this->importExportHelper()->chooseImportOptions('Customer Addresses', 'Add/Update Complex Data');
        //Generated CSV data
        $userWoAddressCsv = $this->loadDataSet('ImportExport', 'generic_address_csv', array(
            '_entity_id' => '',
            '_email' => $userWithoutAddress['email'],
            'city' => 'Lincoln',
            'country_id' => 'US',
            'firstname' => 'Jana',
            'lastname' => 'Johnson',
            'postcode' => '90232',
            'street' => "4955 Crummit\nLane",
            'telephone' => '402-219-4835'
        ));
        $unformattedStreet = $userWoAddressCsv['street'];
        $userWoAddressCsv['street'] = stripcslashes($userWoAddressCsv['street']);
        $userWithAddressCsv = $this->loadDataSet('ImportExport', 'generic_address_csv', array(
            '_entity_id' => $addressIdExisting,
            '_email' => $userWithAddressData['email'],
            'city' => 'Milwaukee',
            'country_id' => 'US',
            'firstname' => 'Kim',
            'lastname' => 'Montgomery',
            'postcode' => '53213',
            'street' => "593 Grant View Drive",
            'telephone' => '414-411-2378'
        ));
        //Build CSV array
        $data = array($userWoAddressCsv, $userWithAddressCsv);
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
        $this->customerHelper()->openCustomer(array('email' => $userWithoutAddress['email']));
        $newAddressData = array(
            'city' => $data[0]['city'],
            'first_name' => $data[0]['firstname'],
            'last_name' => $data[0]['lastname'],
            'zip_code' => $data[0]['postcode'],
            'street_address_line_1' => substr($unformattedStreet, 0, strpos($unformattedStreet, "\n")),
            'street_address_line_2' => substr($unformattedStreet, strpos($unformattedStreet, "\n") + 1),
            'telephone' => $data[0]['telephone']
        );
        //Verify customer account address
        $this->openTab('addresses');
        $this->assertNotEquals(0, $this->customerHelper()->isAddressPresent($newAddressData),
            'New customer address has not been created ' . print_r($newAddressData, true));
        //Verify customer account
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => $userWithAddressData['email']));
        $existingAddressData['city'] = $data[1]['city'];
        $existingAddressData['first_name'] = $data[1]['firstname'];
        $existingAddressData['last_name'] = $data[1]['lastname'];
        $existingAddressData['zip_code'] = $data[1]['postcode'];
        $existingAddressData['street_address_line_1'] = $data[1]['street'];
        $existingAddressData['street_address_line_2'] = '';
        $existingAddressData['telephone'] = $data[1]['telephone'];
        $existingAddressData['state'] = $data[1]['region'];
        $this->openTab('addresses');
        $this->assertNotEquals(0, $this->customerHelper()->isAddressPresent($existingAddressData),
            'Existent customer address has not been updated ' . print_r($existingAddressData, true));
    }

    /**
     * Partial Import
     *
     * @test
     * @dataProvider partialImportData
     * @TestlinkId TL-MAGE-5636
     */
    public function partialImport($csvData, $newAddressData, $validation)
    {
        //Set correct email for csv data
        foreach ($csvData as $key => $value) {
            if (array_key_exists('_email', $value) && $value['_email'] == '<realEmail>') {
                $csvData[$key]['_email'] = self::$_customerData['email'];
            }
        }
        //Step 1
        $this->navigate('import');
        //Steps 2-4
        $this->importExportHelper()->chooseImportOptions('Customer Addresses', 'Add/Update Complex Data');
        //Steps 5-6
        $importData = $this->importExportHelper()->import($csvData);
        //Verifying import
        $this->assertEquals($validation, $importData, 'Import has been finished with issues');
        //Step 7
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => self::$_customerData['email']));
        $this->openTab('addresses');
        //Verifying if new address is present
        $this->assertNotEquals(0, $this->customerHelper()->isAddressPresent($newAddressData),
            'New customer address has not been created');
    }

    public function partialImportData()
    {
        $csv[0][0] = $this->loadDataSet('ImportExport', 'generic_address_csv', array(
            '_email' => '<realEmail>', 'city' => 'Camden', 'company' => 'Team Electronics',
            'fax' => '609-504-6350', 'firstname' => 'William', 'lastname' => 'Holler', 'middlename' => 'E.',
            'postcode' => '08102', 'prefix' => '', 'region' => 'New Jersey', 'street' => '3186 Lincoln Street',
            'telephone' => '609-504-6350'
        ));
        $newAddressData[0] = $this->loadDataSet('Customers', 'generic_address', array(
            'first_name' => 'William', 'middle_name' => 'E.', 'last_name' => 'Holler',
            'company' => 'Team Electronics', 'street_address_line_1' => '3186 Lincoln Street',
            'street_address_line_2' => '', 'city' => 'Camden', 'state' => 'New Jersey', 'zip_code' => '08102',
            'telephone' => '609-504-6350', 'fax' => '609-504-6350'
        ));
        $csv[0][1] = $this->loadDataSet('ImportExport', 'generic_address_csv',
            array('_email' => '<realEmail>', '_website' => 'invalid',));
        $csvFile[0] = array($csv[0][0], $csv[0][1]);
        $message[0] = array(
            'validation' => array(
                'error' => array("Invalid value in website column in rows: 2"),
                'validation' => array(
                    "Please fix errors and re-upload file or simply press \"Import\" button to skip rows with errors  Import",
                    "Checked rows: 2, checked entities: 2, invalid rows: 1, total errors: 1",
                )
            ),
            'import' => array('success' => array('Import successfully done')
            )
        );
        $csv[1][0] = $this->loadDataSet('ImportExport', 'generic_address_csv', array(
            '_email' => '<realEmail>', 'city' => 'San Antonio', 'company' => 'Wherehouse Music',
            'fax' => '210-315-4837', 'firstname' => 'Cora', 'lastname' => 'Robles', 'middlename' => 'K.',
            'postcode' => '78258', 'prefix' => '', 'region' => 'Texas', 'street' => '1506 Weekley Street',
            'telephone' => '210-315-4837'
        ));
        $newAddressData[1] = $this->loadDataSet('Customers', 'generic_address', array(
            'first_name' => 'Cora', 'middle_name' => 'K.', 'last_name' => 'Robles',
            'company' => 'Wherehouse Music', 'street_address_line_1' => '1506 Weekley Street',
            'street_address_line_2' => '', 'city' => 'San Antonio', 'state' => 'Texas', 'zip_code' => '78258',
            'telephone' => '210-315-4837', 'fax' => '210-315-4837'
        ));
        $csv[1][1] = $this->loadDataSet('ImportExport', 'generic_address_csv', array('_email' => '',));
        $csvFile[1] = array($csv[1][0], $csv[1][1]);
        $message[1] = array(
            'validation' => array(
                'error' => array("E-mail is not specified in rows: 2"),
                'validation' => array(
                    "Please fix errors and re-upload file or simply press \"Import\" button to skip rows with errors  Import",
                    "Checked rows: 2, checked entities: 2, invalid rows: 1, total errors: 1",
                )
            ),
            'import' => array('success' => array('Import successfully done'))
        );
        return array(
            array($csvFile[0], $newAddressData[0], $message[0]),
            array($csvFile[1], $newAddressData[1], $message[1]),
        );
    }
}
