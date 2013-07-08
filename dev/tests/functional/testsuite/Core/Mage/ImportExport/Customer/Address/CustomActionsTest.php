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
class Core_Mage_ImportExport_Customer_Address_CustomActionsTest extends Mage_Selenium_TestCase
{
    static protected $_customersUpdateData = array();
    static protected $_customersEmptyData = array();
    static protected $_customersDeleteData = array();

    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->runMassAction('Delete', 'all', 'confirmation_for_massaction_delete');
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
     * Precondition for updateActionImport test
     *
     * @test
     */
    public function preconditionUpdate()
    {
        for ($i = 0; $i < 2; $i++) {
            $userUpdateData = $this->loadDataSet('Customers', 'generic_customer_account');
            $userFirstAddressData = $this->loadDataSet('Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:'), 'middle_name' => 'C.'));
            $this->navigate('manage_customers');
            $this->customerHelper()->createCustomer($userUpdateData, $userFirstAddressData);
            $this->assertMessagePresent('success', 'success_saved_customer');
            $this->customerHelper()->openCustomer(array('email' => $userUpdateData['email']));
            $this->openTab('addresses');
            self::$_customersUpdateData[] = array(
                'email' => $userUpdateData['email'],
                'first_name' => $userUpdateData['first_name'],
                'last_name' => $userUpdateData['last_name'],
                'address_id' => $this->customerHelper()->isAddressPresent($userFirstAddressData),
                'address' => $userFirstAddressData
            );
        }
        for ($i = 0; $i < 1; $i++) {
            $userUpdateData = $this->loadDataSet('Customers', 'generic_customer_account');
            $userAddressData[0] = $this->loadDataSet('Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:'), 'middle_name' => 'C.'));
            $userAddressData[1] = $this->loadDataSet('Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:'), 'middle_name' => 'C.'));
            $this->navigate('manage_customers');
            $this->customerHelper()->createCustomer($userUpdateData, $userAddressData[0]);
            $this->assertMessagePresent('success', 'success_saved_customer');
            $this->customerHelper()->openCustomer(array('email' => $userUpdateData['email']));
            $this->customerHelper()->addAddress($userAddressData[1]);
            $this->saveForm('save_customer');
            $this->customerHelper()->openCustomer(array('email' => $userUpdateData['email']));
            $this->openTab('addresses');
            self::$_customersUpdateData[] = array(
                'email' => $userUpdateData['email'],
                'first_name' => $userUpdateData['first_name'],
                'last_name' => $userUpdateData['last_name'],
                'address_id' => $this->customerHelper()->isAddressPresent($userAddressData[0]),
                'address' => $userAddressData[0]
            );
            self::$_customersUpdateData[] = array(
                'email' => $userUpdateData['email'],
                'first_name' => $userUpdateData['first_name'],
                'last_name' => $userUpdateData['last_name'],
                'address_id' => $this->customerHelper()->isAddressPresent($userAddressData[1]),
                'address' => $userAddressData[1]
            );
        }
    }

    /**
     * Custom import: update addresses
     * Need to verify that the customer addresses are updated if the action is "Update"
     *
     * @test
     * @depends preconditionUpdate
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
        $userAddressData = $this->loadDataSet('ImportExport', 'generic_address',
            array('middle_name' => 'C.'));
        for ($i = 0; $i < 4; $i++) {
            $this->navigate('manage_customers');
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
            $this->openTab('addresses');
            $this->assertNotEquals(0, $this->customerHelper()->isAddressPresent($userAddressData),
                "Address not found for address data " . print_r($userAddressData, true) . print_r($data[$i], true));
        }
    }

    /**
     * Precondition for emptyActionImport test
     *
     * @test
     */
    public function preconditionEmpty()
    {
        for ($i = 0; $i < 1; $i++) {
            $this->navigate('manage_customers');
            $userEmptyData = $this->loadDataSet('Customers', 'generic_customer_account');
            $userAddressData[0] = $this->loadDataSet('Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:'), 'middle_name' => 'C.'));
            $userAddressData[1] = $this->loadDataSet('Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:'), 'middle_name' => 'C.'));
            $this->customerHelper()->createCustomer($userEmptyData, $userAddressData[0]);
            $this->assertMessagePresent('success', 'success_saved_customer');
            $this->customerHelper()->openCustomer(array('email' => $userEmptyData['email']));
            $this->customerHelper()->addAddress($userAddressData[1]);
            $this->saveForm('save_customer');
            $this->customerHelper()->openCustomer(array('email' => $userEmptyData['email']));
            $this->openTab('addresses');
            self::$_customersEmptyData[] = array(
                'email' => $userEmptyData['email'],
                'first_name' => $userEmptyData['first_name'],
                'last_name' => $userEmptyData['last_name'],
                'address_id' => $this->customerHelper()->isAddressPresent($userAddressData[0]),
                'address' => $userAddressData[0]
            );
            self::$_customersEmptyData[] = array(
                'email' => $userEmptyData['email'],
                'first_name' => $userEmptyData['first_name'],
                'last_name' => $userEmptyData['last_name'],
                'address_id' => $this->customerHelper()->isAddressPresent($userAddressData[1]),
                'address' => $userAddressData[1]
            );
        }
    }

    /**
     * Custom import: not recognized or empty action
     *
     * @test
     * @depends preconditionEmpty
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
        $this->customerHelper()->openCustomer(array('email' => self::$_customersEmptyData[0]['email']));
        $this->openTab('addresses');
        for ($i = 0; $i < count($data); $i++) {
            $addressAttributes = array(
                'first_name',
                'last_name',
                'middle_name',
                'city',
                'zip_code',
                'telephone',
                'street_address_line_1',
                'street_address_line_2',
                'state',
            );
            $addressCsvAttributes = array(
                'firstname',
                'lastname',
                'middlename',
                'city',
                'postcode',
                'telephone',
                'street',
                'street',
                'region',
            );
            foreach ($addressCsvAttributes as $key => $attribute) {
                if ($data[$i][$attribute] != '') {
                    $userAddressData[$addressAttributes[$key]] = $data[$i][$attribute];
                    if ($addressAttributes[$key] == 'street_address_line_2') {
                        $userAddressData[$addressAttributes[$key]] = '';
                    }
                } else {
                    $userAddressData[$addressAttributes[$key]] =
                        self::$_customersEmptyData[$i]['address'][$addressAttributes[$key]];
                }
            }
            if ($data[$i]['_website'] == 'invalid') {
                $this->assertEquals(0, $this->customerHelper()->isAddressPresent($userAddressData),
                    "Address found for data =\n" . print_r($userAddressData, true) . "csv data =\n"
                        . print_r($data[$i], true));
            } else {
                $this->assertNotEquals(0, $this->customerHelper()->isAddressPresent($userAddressData),
                    "Address not found for data =\n" . print_r($userAddressData, true) . "csv data =\n"
                        . print_r($data[$i], true));
            }
        }
    }

    /**
     * Precondition for deleteActionImport test
     *
     * @test
     */
    public function preconditionDelete()
    {
        for ($i = 0; $i < 1; $i++) {
            $this->navigate('manage_customers');
            $userDeleteData = $this->loadDataSet('Customers', 'generic_customer_account');
            $userAddressData[0] = $this->loadDataSet('Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:')));
            $userAddressData[1] = $this->loadDataSet('Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:')));
            $userAddressData[2] = $this->loadDataSet('Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:')));
            $this->customerHelper()->createCustomer($userDeleteData, $userAddressData[0]);
            $this->assertMessagePresent('success', 'success_saved_customer');
            $this->customerHelper()->openCustomer(array('email' => $userDeleteData['email']));
            $this->customerHelper()->addAddress($userAddressData[1]);
            $this->saveForm('save_customer');
            $this->customerHelper()->openCustomer(array('email' => $userDeleteData['email']));
            $this->customerHelper()->addAddress($userAddressData[2]);
            $this->saveForm('save_customer');
            $this->customerHelper()->openCustomer(array('email' => $userDeleteData['email']));
            $this->openTab('addresses');
            self::$_customersDeleteData[] = array(
                'email' => $userDeleteData['email'],
                'first_name' => $userDeleteData['first_name'],
                'last_name' => $userDeleteData['last_name'],
                'address_id' => $this->customerHelper()->isAddressPresent($userAddressData[0]),
                'address' => $userAddressData[0]
            );
            self::$_customersDeleteData[] = array(
                'email' => $userDeleteData['email'],
                'first_name' => $userDeleteData['first_name'],
                'last_name' => $userDeleteData['last_name'],
                'address_id' => $this->customerHelper()->isAddressPresent($userAddressData[1]),
                'address' => $userAddressData[1]
            );
            self::$_customersDeleteData[] = array(
                'email' => $userDeleteData['email'],
                'first_name' => $userDeleteData['first_name'],
                'last_name' => $userDeleteData['last_name'],
                'address_id' => $this->customerHelper()->isAddressPresent($userAddressData[2]),
                'address' => $userAddressData[2]
            );
        }
    }

    /**
     * Custom import: delete addresses
     * Verify that deleting customer address via import (custom behavior) works correctly
     *
     * @test
     * @depends preconditionDelete
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
        $this->customerHelper()->openCustomer(array('email' => self::$_customersDeleteData[0]['email']));
        $this->openTab('addresses');
        $this->assertEquals(0, $this->customerHelper()->isAddressPresent(self::$_customersDeleteData[0]['address']),
            'Address found for: ' . print_r(self::$_customersDeleteData[0]['address'], true));
        $this->assertEquals(0, $this->customerHelper()->isAddressPresent(self::$_customersDeleteData[1]['address']),
            'Address found for: ' . print_r(self::$_customersDeleteData[1]['address'], true));
        $this->assertNotEquals(0, $this->customerHelper()->isAddressPresent(self::$_customersDeleteData[2]['address']),
            'Address not found for: ' . print_r(self::$_customersDeleteData[2]['address'], true));
        $this->clearMessages();
    }

    public function importUpdateData()
    {
        return array(
            array(
                array(
                    $this->loadDataSet('ImportExport', 'generic_address_csv', array(
                        '_email' => '<realEmail>', 'postcode' => '10001', 'street' => 'ave 250',
                        'company' => '', 'fax' => '', 'middlename' => '', 'prefix' => '',
                        '_address_default_billing_' => '', '_address_default_shipping_' => '',
                        '_action' => 'Update'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_address_csv', array(
                        '_email' => '<realEmail>', 'postcode' => '10002', 'street' => 'ave 250',
                        'company' => '', 'fax' => '', 'middlename' => '', 'prefix' => '',
                        '_address_default_billing_' => '', '_address_default_shipping_' => '',
                        '_entity_id' => '<realEntityID>', '_action' => 'UpDaTe'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_address_csv', array(
                        '_email' => '<realEmail>', 'postcode' => '10003',
                        'street' => 'ave 250', 'company' => '', 'fax' => '',
                        'middlename' => '', 'prefix' => '', '_address_default_billing_' => '',
                        '_address_default_shipping_' => '', '_entity_id' => '<realEntityID>',
                        '_action' => 'update'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_address_csv', array(
                        '_email' => '<realEmail>', 'postcode' => '10004',
                        'street' => 'ave 250', 'company' => '', 'fax' => '',
                        'middlename' => '', 'prefix' => '', '_address_default_billing_' => '',
                        '_address_default_shipping_' => '', '_entity_id' => '<realEntityID>',
                        '_action' => 'Update'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_address_csv', array(
                        '_email' => 'nonexistsemail.com', 'postcode' => '10005',
                        'street' => 'ave 250', 'company' => '', 'fax' => '',
                        'middlename' => '', 'prefix' => '', '_address_default_billing_' => '',
                        '_address_default_shipping_' => '', '_action' => 'Update'
                    ))
                )
            )
        );
    }

    public function importEmptyData()
    {
        return array(
            array(
                array(
                    $this->loadDataSet('ImportExport', 'generic_address_csv', array(
                        '_email' => '<realEmail>', 'postcode' => '10005',
                        '_entity_id' => '<realEntityID>', '_action' => ''
                    )),
                    $this->loadDataSet('ImportExport', 'generic_address_csv', array(
                        '_email' => '<realEmail>', 'region' => '', 'city' => '',
                        'country_id' => '', 'firstname' => '', 'lastname' => '', 'postcode' => '',
                        'street' => '', 'telephone' => '', 'company' => '', 'fax' => '',
                        'middlename' => '', 'prefix' => '', '_address_default_billing_' => '',
                        '_address_default_shipping_' => '', '_entity_id' => '<realEntityID>',
                        '_action' => 'Please, delete'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array('_email' => '<realEmail>', 'postcode' => '10007', '_entity_id' => '', '_action' => '')),
                    $this->loadDataSet('ImportExport', 'generic_address_csv', array(
                        '_website' => 'invalid', '_email' => '<realEmail>', 'postcode' => '10008',
                        '_entity_id' => '', '_action' => 'Please, delete'
                    ))
                )
            )
        );
    }

    public function importDeleteData()
    {
        return array(
            array(
                array(
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array('_email' => '<realEmail>', '_entity_id' => '<realEntityID>', '_action' => 'Delete')),
                    $this->loadDataSet('ImportExport', 'generic_address_csv', array(
                        '_email' => '<realEmail>', 'region' => '', 'city' => '',
                        'country_id' => '', 'firstname' => '', 'lastname' => '', 'postcode' => '',
                        'street' => '', 'telephone' => '', 'company' => '', 'fax' => '',
                        'middlename' => '', 'prefix' => '', '_address_default_billing_' => '',
                        '_address_default_shipping_' => '', '_entity_id' => '<realEntityID>',
                        '_action' => 'delete'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_address_csv', array(
                        '_website' => 'invalid', '_email' => '<realEmail>',
                        '_entity_id' => '<realEntityID>', '_action' => 'delete'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array('_email' => '<realEmail>', '_entity_id' => '', '_action' => 'delete'))
                )
            )
        );
    }
}