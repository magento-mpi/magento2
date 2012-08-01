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
 * Custom Actions Addresses Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_ImportExport_CustomActions_AddressTest extends Mage_Selenium_TestCase
{
    static protected $_customersUpdateData = array();
    static protected $_customersEmptyData = array();
    static protected $_customersDeleteData = array();

    /**
     * Set preconditions to run tests
     * Create test customers
     */
    public function setUpBeforeTests()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        for ($i = 0; $i < 2; $i++) {
            $this->navigate('manage_customers');
            $userData = $this->loadDataSet('Customers', 'generic_customer_account');
            $userFirstAddressData = $this->loadDataSet('Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:')));
            $this->customerHelper()->createCustomer($userData, $userFirstAddressData);
            $this->assertMessagePresent('success', 'success_saved_customer');
            $this->addParameter('customer_first_last_name', $userData['first_name'] . ' ' . $userData['last_name']);
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            self::$_customersUpdateData[] =
                array('email'      => $userData['email'],
                      'first_name' => $userData['first_name'],
                      'last_name'  => $userData['last_name'],
                      'address_id' => $this->customerHelper()->isAddressPresent($userFirstAddressData),
                      'address'    => $userFirstAddressData);
        }
        for ($i = 0; $i < 1; $i++) {
            $this->navigate('manage_customers');
            $userData = $this->loadDataSet('Customers', 'generic_customer_account');
            $userFirstAddressData = $this->loadDataSet('Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:')));
            $userSecondAddressData = $this->loadDataSet(
                'Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:'))
            );
            $this->customerHelper()->createCustomer($userData, $userFirstAddressData);
            $this->assertMessagePresent('success', 'success_saved_customer');
            $this->addParameter('customer_first_last_name', $userData['first_name'] . ' ' . $userData['last_name']);
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            $this->customerHelper()->addAddress($userSecondAddressData);
            $this->customerHelper()->saveForm('save_customer');
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            self::$_customersUpdateData[] =
                array('email'      => $userData['email'],
                      'first_name' => $userData['first_name'],
                      'last_name'  => $userData['last_name'],
                      'address_id' => $this->customerHelper()->isAddressPresent($userFirstAddressData),
                      'address'    => $userFirstAddressData);
            self::$_customersUpdateData[] =
                array('email'      => $userData['email'],
                      'first_name' => $userData['first_name'],
                      'last_name'  => $userData['last_name'],
                      'address_id' => $this->customerHelper()->isAddressPresent($userSecondAddressData),
                      'address' => $userSecondAddressData);
        }

        for ($i = 0; $i < 1; $i++) {
            $this->navigate('manage_customers');
            $userData = $this->loadDataSet('Customers', 'generic_customer_account');
            $userFirstAddressData = $this->loadDataSet('Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:')));
            $userSecondAddressData = $this->loadDataSet('Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:')));
            $this->customerHelper()->createCustomer($userData, $userFirstAddressData);
            $this->assertMessagePresent('success', 'success_saved_customer');
            $this->addParameter('customer_first_last_name', $userData['first_name'] . ' ' . $userData['last_name']);
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            $this->customerHelper()->addAddress($userSecondAddressData);
            $this->customerHelper()->saveForm('save_customer');
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            self::$_customersEmptyData[] =
                array('email'      => $userData['email'],
                      'first_name' => $userData['first_name'],
                      'last_name'  => $userData['last_name'],
                      'address_id' => $this->customerHelper()->isAddressPresent($userFirstAddressData),
                      'address'    => $userFirstAddressData);
            self::$_customersEmptyData[] =
                array('email'      => $userData['email'],
                      'first_name' => $userData['first_name'],
                      'last_name'  => $userData['last_name'],
                      'address_id' => $this->customerHelper()->isAddressPresent($userSecondAddressData),
                      'address'    => $userSecondAddressData);
        }
        for ($i = 0; $i < 1; $i++) {
            $this->navigate('manage_customers');
            $userData = $this->loadDataSet('Customers', 'generic_customer_account');
            $userFirstAddressData = $this->loadDataSet('Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:')));
            $userSecondAddressData = $this->loadDataSet('Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:')));
            $userThirdAddressData = $this->loadDataSet('Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:')));
            $this->customerHelper()->createCustomer($userData, $userFirstAddressData);
            $this->assertMessagePresent('success', 'success_saved_customer');
            $this->addParameter('customer_first_last_name', $userData['first_name'] . ' ' . $userData['last_name']);
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            $this->customerHelper()->addAddress($userSecondAddressData);
            $this->customerHelper()->saveForm('save_customer');
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            $this->customerHelper()->addAddress($userThirdAddressData);
            $this->customerHelper()->saveForm('save_customer');
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            self::$_customersDeleteData[] = array('email'      => $userData['email'],
                                                 'first_name' => $userData['first_name'],
                                                 'last_name'  => $userData['last_name'],
                                                 'address_id' => $this->customerHelper()
                                                     ->isAddressPresent($userFirstAddressData),
                                                 'address'    => $userFirstAddressData);
            self::$_customersDeleteData[] = array('email'      => $userData['email'],
                                                 'first_name' => $userData['first_name'],
                                                 'last_name'  => $userData['last_name'],
                                                 'address_id' => $this->customerHelper()
                                                     ->isAddressPresent($userSecondAddressData),
                                                 'address'    => $userSecondAddressData);
            self::$_customersDeleteData[] = array('email'      => $userData['email'],
                                                 'first_name' => $userData['first_name'],
                                                 'last_name'  => $userData['last_name'],
                                                 'address_id' => $this->customerHelper()
                                                     ->isAddressPresent($userThirdAddressData),
                                                 'address'    => $userThirdAddressData);
        }
    }

    /**
     * Preconditions:
     * Log in to Backend.
     * Navigate to System -> Export
     */
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        //Step 1
        $this->navigate('import');
    }

    /**
     * Custom import: update addresses
     * Need to verify that the customer addresses are updated if the action is "Update"
     * After steps 
     * Verify that all Customers Addresses information was imported
     *
     * @test
     * @dataProvider importUpdateData
     * @TestlinkId TL-MAGE-5686
     */
    public function updateActionImport(array $data)
    {
        //Precondition: set data for CSV
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['_email'] == '<realEmail>') {
                $data[$i]['_email'] = self::$_customersUpdateData[$i]['email'];
            }
            if ($data[$i]['_entity_id'] == '<realEntityID>') {
                $data[$i]['_entity_id'] = self::$_customersUpdateData[$i]['address_id'];
            }
        }
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Customer Addresses', 'Custom Action');
        //Step 5, 6, 7
        $importResult = $this->importExportHelper()->import($data);
        //Check import
        $this->assertArrayHasKey('validation', $importResult,
            'Import has been finished without issues: ' . print_r($importResult, true));
        $this->assertArrayHasKey('error', $importResult['validation'],
            'Import has been finished without issues: ' . print_r($importResult, true));
        $this->assertEquals("E-mail is invalid in rows: 5", $importResult['validation']['error'][0],
            'Import has been finished with issues: ' . print_r($importResult, true));
        $this->assertArrayHasKey('import', $importResult,
            'Import has been finished with issues: ' . print_r($importResult, true));
        $this->assertArrayHasKey('success', $importResult['import'],
            'Import has been finished with issues: ' . print_r($importResult, true));
        //Verifying
        $userAddressData = $this->loadDataSet('ImportExport', 'generic_address');
        for ($i = 0; $i < 4; $i++) {
            $this->navigate('manage_customers');
            $this->addParameter('customer_first_last_name',
                self::$_customersUpdateData[$i]['first_name'] . ' ' . self::$_customersUpdateData[$i]['last_name']
            );
            $this->customerHelper()->openCustomer(array('email' => self::$_customersUpdateData[$i]['email']));
            $userAddressData['first_name'] = $data[$i]['firstname'];
            $userAddressData['last_name'] = $data[$i]['lastname'];
            $userAddressData['city'] = $data[$i]['city'];
            $userAddressData['zip_code'] = $data[$i]['postcode'];
            $userAddressData['telephone'] = $data[$i]['telephone'];
            $userAddressData['street_address_line_1'] = $data[$i]['street'];
            $userAddressData['street_address_line_2'] = '';
            $userAddressData['state'] = $data[$i]['region'];
            if ($data[$i]['_entity_id'] == 'home') {
                $userAddressData['middle_name'] = '';
            }
            $this->assertTrue((bool)$this->customerHelper()->isAddressPresent($userAddressData),
                "Address not found for address data " . print_r($userAddressData, true) . print_r($data[$i], true));
        }
    }

    /**
     * Custom import: not recognized or empty action
     * If action in csv file is empty or not recognized by the system, 'update' action should be used to corresponding
     * csv row
     * After steps:
     * Verify that all Customers Addresses information was imported
     *
     * @test
     * @dataProvider importEmptyData
     * @TestlinkId TL-MAGE-5688
     */
    public function emptyActionImport(array $data)
    {
        //Precondition: set data for CSV
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['_email'] == '<realEmail>') {
                $data[$i]['_email'] = self::$_customersEmptyData[0]['email'];
            }
            if ($data[$i]['_entity_id'] == '<realEntityID>') {
                $data[$i]['_entity_id'] = self::$_customersEmptyData[$i]['address_id'];
            }
        }
        $this->navigate('import');
        $this->importExportHelper()
            ->chooseImportOptions('Customer Addresses', 'Custom Action');
        //Step 5, 6, 7
        $importResult = $this->importExportHelper()->import($data);
        //Check import
        $this->assertArrayHasKey('validation', $importResult,
            'Import has been finished without issues: ' . print_r($importResult, true));
        $this->assertArrayHasKey('error', $importResult['validation'],
            'Import has been finished without issues: ' . print_r($importResult, true));
        $this->assertEquals("Invalid value in website column in rows: 4", $importResult['validation']['error'][0],
            'Import has been finished with issues: ' . print_r($importResult, true));
        $this->assertArrayHasKey('import', $importResult,
            'Import has been finished with issues: ' . print_r($importResult, true));
        $this->assertArrayHasKey('success', $importResult['import'],
            'Import has been finished with issues: ' . print_r($importResult, true));
        //Verifying
        $userAddressData = $this->loadDataSet('ImportExport', 'generic_address');
        $this->navigate('manage_customers');
        $this->addParameter('customer_first_last_name',
            self::$_customersEmptyData[0]['first_name'] . ' ' . self::$_customersEmptyData[0]['last_name']);
        $this->customerHelper()->openCustomer(array('email' => self::$_customersEmptyData[0]['email']));
        for ($i = 0; $i < count($data); $i++) {
            $userAddressData['first_name'] = ($data[$i]['firstname'] != '') ? $data[$i]['firstname']
                : self::$_customersEmptyData[$i]['address']['first_name'];
            $userAddressData['last_name'] = ($data[$i]['lastname'] != '') ? $data[$i]['lastname']
                : self::$_customersEmptyData[$i]['address']['last_name'];
            $userAddressData['middle_name'] = ($data[$i]['middlename'] != '') ? $data[$i]['middlename']
                : self::$_customersEmptyData[$i]['address']['middle_name'];
            $userAddressData['city'] =
                ($data[$i]['city'] != '') ? $data[$i]['city'] : self::$_customersEmptyData[$i]['address']['city'];
            $userAddressData['zip_code'] = ($data[$i]['postcode'] != '') ? $data[$i]['postcode']
                : self::$_customersEmptyData[$i]['address']['zip_code'];
            $userAddressData['telephone'] = ($data[$i]['telephone'] != '') ? $data[$i]['telephone']
                : self::$_customersEmptyData[$i]['address']['telephone'];
            $userAddressData['street_address_line_1'] = ($data[$i]['street'] != '') ? $data[$i]['street']
                : self::$_customersEmptyData[$i]['address']['street_address_line_1'];
            $userAddressData['street_address_line_2'] =
                ($data[$i]['street'] != '') ? '' : self::$_customersEmptyData[$i]['address']['street_address_line_2'];
            $userAddressData['state'] =
                ($data[$i]['region'] != '') ? $data[$i]['region'] : self::$_customersEmptyData[$i]['address']['state'];
            if ($data[$i]['_website'] == 'invalid') {
                $this->assertFalse((bool)$this->customerHelper()->isAddressPresent($userAddressData),
                    "Address found for address data =\n" . print_r($userAddressData, true) . "csv data =\n"
                    . print_r($data[$i], true));
                $this->clearMessages();
            } else {
                $this->assertTrue((bool)$this->customerHelper()->isAddressPresent($userAddressData),
                    "Address not found for" . print_r($userAddressData, true) . print_r($data[$i], true));
            }
        }
    }

    /**
     * Custom import: delete addresses
     * Verify that deleting customer address via import (custom behavior) works correctly
     * After steps 
     * Verify that all Customers addresses information was deleted
     *
     * @test
     * @dataProvider importDeleteData
     * @TestlinkId TL-MAGE-5687
     */
    public function deleteActionImport(array $data)
    {
        //Precondition: set data for CSV
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['_email'] == '<realEmail>') {
                $data[$i]['_email'] = self::$_customersDeleteData[0]['email'];
            }
            if ($data[$i]['_entity_id'] == '<realEntityID>') {
                $data[$i]['_entity_id'] = self::$_customersDeleteData[$i]['address_id'];
            }
        }
        $this->navigate('import');
        $this->importExportHelper()
            ->chooseImportOptions('Customer Addresses', 'Custom Action');
        //Step 5, 6, 7
        $importResult = $this->importExportHelper()->import($data);
        //Check import
        $this->assertArrayHasKey('validation', $importResult,
            'Import has been finished without issues: ' . print_r($importResult, true));
        $this->assertArrayHasKey('error', $importResult['validation'],
            'Import has been finished without issues: ' . print_r($importResult, true));
        $this->assertEquals("Invalid value in website column in rows: 3", $importResult['validation']['error'][0],
            'Import has been finished with issues: ' . print_r($importResult, true));
        $this->assertEquals("Customer address id column is not specified in rows: 4",
            $importResult['validation']['error'][1],
            'Import has been finished with issues: ' . print_r($importResult, true));
        $this->assertArrayHasKey('import', $importResult,
            'Import has been finished with issues: ' . print_r($importResult, true));
        $this->assertArrayHasKey('success', $importResult['import'],
            'Import has been finished with issues: ' . print_r($importResult, true));
        //Verifying
        $this->navigate('manage_customers');
        $this->addParameter('customer_first_last_name',
            self::$_customersDeleteData[0]['first_name'] . ' ' . self::$_customersDeleteData[0]['last_name']);
        $this->customerHelper()->openCustomer(array('email' => self::$_customersDeleteData[0]['email']));
        $this->assertFalse((bool)$this->customerHelper()->isAddressPresent(self::$_customersDeleteData[0]['address']),
            'Address found for: ' . print_r(self::$_customersDeleteData[0]['address'], true));
        $this->assertFalse((bool)$this->customerHelper()->isAddressPresent(self::$_customersDeleteData[1]['address']),
            'Address found for: ' . print_r(self::$_customersDeleteData[1]['address'], true));
        $this->assertTrue((bool)$this->customerHelper()->isAddressPresent(self::$_customersDeleteData[2]['address']),
            'Address not found for: ' . print_r(self::$_customersDeleteData[2]['address'], true));
        $this->clearMessages();
    }

    public function importUpdateData()
    {
        return array(
            array(
                array(
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array('_email'                     => '<realEmail>', 'postcode' => '10001',
                              'street'                     => 'ave 250', 'company' => '', 'fax'    => '',
                              'middlename'                 => '', 'prefix' => '', '_address_default_billing_' => '',
                              '_address_default_shipping_' => '', '_action' => 'Update')),
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array('_email'                     => '<realEmail>', 'postcode' => '10002',
                              'street'                     => 'ave 250', 'company' => '', 'fax'    => '',
                              'middlename'                 => '', 'prefix' => '', '_address_default_billing_' => '',
                              '_address_default_shipping_' => '', '_entity_id' => '<realEntityID>',
                              '_action'                    => 'UpDaTe')),
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array('_email'                     => '<realEmail>', 'postcode' => '10003',
                              'street'                     => 'ave 250', 'company' => '', 'fax'    => '',
                              'middlename'                 => '', 'prefix' => '', '_address_default_billing_' => '',
                              '_address_default_shipping_' => '', '_entity_id' => '<realEntityID>',
                              '_action'                    => 'update')),
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array('_email'                     => '<realEmail>', 'postcode' => '10004',
                              'street'                     => 'ave 250', 'company' => '', 'fax'    => '',
                              'middlename'                 => '', 'prefix' => '', '_address_default_billing_' => '',
                              '_address_default_shipping_' => '', '_entity_id' => '<realEntityID>',
                              '_action'                    => 'Update')),
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array('_email'                     => 'nonexistsemail.com', 'postcode' => '10005',
                              'street'                     => 'ave 250', 'company' => '', 'fax' => '',
                              'middlename'                 => '', 'prefix' => '', '_address_default_billing_' => '',
                              '_address_default_shipping_' => '', '_action' => 'Update')))));
    }

    public function importEmptyData()
    {
        return array(
            array(
                array(
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array('_email'  => '<realEmail>', 'postcode' => '10005', '_entity_id' => '<realEntityID>',
                              '_action' => '')),
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array('_email'                     => '<realEmail>', 'region' => '', 'city' => '',
                              'country_id'                 => '', 'firstname' => '', 'lastname' => '', 'postcode' => '',
                              'street'                     => '', 'telephone' => '', 'company'   => '', 'fax' => '',
                              'middlename'                 => '', 'prefix' => '', '_address_default_billing_' => '',
                              '_address_default_shipping_' => '', '_entity_id' => '<realEntityID>',
                              '_action'                    => 'Please, delete')),
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array('_email' => '<realEmail>', 'postcode' => '10007', '_entity_id' => '', '_action' => '')),
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array('_website'   => 'invalid', '_email' => '<realEmail>', 'postcode' => '10008',
                              '_entity_id' => '', '_action' => 'Please, delete')))));
    }

    public function importDeleteData()
    {
        return array(
            array(
                array(
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array('_email' => '<realEmail>', '_entity_id' => '<realEntityID>', '_action' => 'Delete')),
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array('_email'                     => '<realEmail>', 'region' => '', 'city' => '',
                              'country_id'                 => '', 'firstname' => '', 'lastname' => '', 'postcode' => '',
                              'street'                     => '', 'telephone' => '', 'company'   => '', 'fax' => '',
                              'middlename'                 => '', 'prefix' => '', '_address_default_billing_' => '',
                              '_address_default_shipping_' => '', '_entity_id' => '<realEntityID>',
                              '_action'                    => 'delete')),
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array('_website' => 'invalid', '_email' => '<realEmail>', '_entity_id' => '<realEntityID>',
                              '_action'  => 'delete')),
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array('_email' => '<realEmail>', '_entity_id' => '', '_action' => 'delete')))));
    }
}