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
 */
class Community2_Mage_ImportExport_CustomActions_AddressTest extends Mage_Selenium_TestCase
{
    static protected $customersUpdateData = array();
    static protected $customersEmptyData = array();
    static protected $customersDeleteData = array();

    public function setUpBeforeTests(){
        //logged in once for all tests
        $this->loginAdminUser();
        for ($i = 0; $i < 2; $i++) {
            $this->admin('manage_customers');
            $userData = $this->loadDataSet('Customers', 'generic_customer_account');
            $userAddressData = $this->loadDataSet('Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:')));
            $this->customerHelper()->createCustomer($userData, $userAddressData);
            $this->assertMessagePresent('success', 'success_saved_customer');
            $this->addParameter(
                'customer_first_last_name',
                $userData['first_name'] . ' ' . $userData['last_name']);
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            self::$customersUpdateData[] = array(
                'email' => $userData['email'],
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'address_id' => $this->customerHelper()->isAddressPresent($userAddressData),
                'address' => $userAddressData
            );
        }
        for ($i = 0; $i < 1; $i++) {
            $this->admin('manage_customers');
            $userData = $this->loadDataSet('Customers', 'generic_customer_account');
            $userAddressData = $this->loadDataSet('Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:')));
            $userAddressData1 = $this->loadDataSet('Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:')));
            $this->customerHelper()->createCustomer($userData, $userAddressData);
            $this->assertMessagePresent('success', 'success_saved_customer');
            $this->addParameter(
                'customer_first_last_name',
                $userData['first_name'] . ' ' . $userData['last_name']);
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            $this->customerHelper()->addAddress($userAddressData1);
            $this->customerHelper()->saveForm('save_customer');
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            self::$customersUpdateData[] = array(
                'email' => $userData['email'],
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'address_id' => $this->customerHelper()->isAddressPresent($userAddressData),
                'address' => $userAddressData
            );
            self::$customersUpdateData[] = array(
                'email' => $userData['email'],
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'address_id' => $this->customerHelper()->isAddressPresent($userAddressData1),
                'address' => $userAddressData1
            );
        }

        for ($i = 0; $i < 1; $i++) {
            $this->admin('manage_customers');
            $userData = $this->loadDataSet('Customers', 'generic_customer_account');
            $userAddressData = $this->loadDataSet('Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:')));
            $userAddressData1 = $this->loadDataSet('Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:')));
            $this->customerHelper()->createCustomer($userData, $userAddressData);
            $this->assertMessagePresent('success', 'success_saved_customer');
            $this->addParameter(
                'customer_first_last_name',
                $userData['first_name'] . ' ' . $userData['last_name']);
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            $this->customerHelper()->addAddress($userAddressData1);
            $this->customerHelper()->saveForm('save_customer');
            $this->customerHelper()->openCustomer(array(
                'email' => $userData['email']));
            self::$customersEmptyData[] = array('email' => $userData['email'],
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'address_id' => $this->customerHelper()->isAddressPresent($userAddressData),
                'address' => $userAddressData
            );
            self::$customersEmptyData[] = array(
                'email' => $userData['email'],
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'address_id' => $this->customerHelper()->isAddressPresent($userAddressData1),
                'address' => $userAddressData1
            );
        }
        for ($i = 0; $i < 1; $i++) {
            $this->admin('manage_customers');
            $userData = $this->loadDataSet('Customers', 'generic_customer_account');
            $userAddressData = $this->loadDataSet('Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:')));
            $userAddressData1 = $this->loadDataSet('Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:')));
            $userAddressData2 = $this->loadDataSet('Customers', 'generic_address',
                array('zip_code' => $this->generate('string', 6, ':digit:')));
            $this->customerHelper()->createCustomer($userData, $userAddressData);
            $this->assertMessagePresent('success', 'success_saved_customer');
            $this->addParameter(
                'customer_first_last_name',
                $userData['first_name'] . ' ' . $userData['last_name']);
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            $this->customerHelper()->addAddress($userAddressData1);
            $this->customerHelper()->saveForm('save_customer');
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            $this->customerHelper()->addAddress($userAddressData2);
            $this->customerHelper()->saveForm('save_customer');
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            self::$customersDeleteData[] = array(
                'email' => $userData['email'],
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'address_id' => $this->customerHelper()->isAddressPresent($userAddressData),
                'address' => $userAddressData
            );
            self::$customersDeleteData[] = array(
                'email' => $userData['email'],
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'address_id' => $this->customerHelper()->isAddressPresent($userAddressData1),
                'address' => $userAddressData1
            );
            self::$customersDeleteData[] = array(
                'email' => $userData['email'],
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'address_id' => $this->customerHelper()->isAddressPresent($userAddressData2),
                'address' => $userAddressData2
            );
        }
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
     * <p>Custom import: update addresses</p>
     * <p>Need to verify that the customer addresses are updated if the action is "Update"</p>
     * <p>After steps </p>
     * <p>Verify that all Customers Addresses information was imported</p>
     *
     * @test
     * @dataProvider importUpdateData
     * @TestlinkId TL-MAGE-5686
     */
    public function updateActionImport(array $data)
    {
        //Precondition: set data for CSV
        for ($i= 0; $i < count($data); $i++){
            if ($data[$i]['_email'] == '<realEmail>'){
                $data[$i]['_email'] = self::$customersUpdateData[$i]['email'];
            }
            if ($data[$i]['_entity_id'] == '<realEntityID>'){
                $data[$i]['_entity_id'] = self::$customersUpdateData[$i]['address_id'];
            }
        }
        $this->admin('import');
        $this->importExportHelper()->chooseImportOptions('Customers', 'Custom Action',
            'Magento 2.0 format', 'Customer Addresses');
        //Step 5, 6, 7
        $importResult = $this->importExportHelper()->import($data);
        //Check import
        $this->assertArrayHasKey('validation', $importResult, 'Import has been finished without issues: '
            . print_r($importResult));
        $this->assertArrayHasKey('error', $importResult['validation'], 'Import has been finished without issues: '
            . print_r($importResult));
        $this->assertEquals(
            "E-mail is invalid in rows: 5",
            $importResult['validation']['error'][0],
            'Import has been finished with issues: ' . print_r($importResult));
        $this->assertArrayHasKey('import', $importResult, 'Import has been finished with issues: '
            . print_r($importResult));
        $this->assertArrayHasKey('success', $importResult['import'], 'Import has been finished with issues: '
            . print_r($importResult));
        //Verifying
        $userAddressData = $this->loadDataSet('ImportExport', 'generic_address');
        for ($i = 0; $i < 4; $i++){
             $this->admin('manage_customers');
             $this->addParameter(
                 'customer_first_last_name',
                 self::$customersUpdateData[$i]['first_name'] . ' ' . self::$customersUpdateData[$i]['last_name']
             );
             $this->customerHelper()->openCustomer(array('email' => self::$customersUpdateData[$i]['email']));
             $userAddressData['first_name'] = $data[$i]['firstname'];
             $userAddressData['last_name'] = $data[$i]['lastname'];
             $userAddressData['city'] = $data[$i]['city'];
             $userAddressData['zip_code'] = $data[$i]['postcode'];
             $userAddressData['telephone'] = $data[$i]['telephone'];
             $userAddressData['street_address_line_1'] = $data[$i]['street'];
             $userAddressData['street_address_line_2'] = '';
             $userAddressData['state'] = $data[$i]['region'];
             if ($data[$i]['_entity_id'] == 'home'){
                 $userAddressData['middle_name'] = '';
             }
             $this->assertTrue((bool) $this->customerHelper()->isAddressPresent($userAddressData),
                             "Address not found for address data " . print_r($userAddressData) .
                             print_r($data[$i]));
        }
    }
    /**
     * <p>Custom import: not recognized or empty action</p>
     * <p> If action in csv file is empty or not recognized by the system, 'update' action should be used to corresponding csv row</p>
     * <p>After steps </p>
     * <p>Verify that all Customers Addresses information was imported</p>
     *
     * @test
     * @dataProvider importEmptyData
     * @TestlinkId TL-MAGE-5688
     */
    public function emptyActionImport(array $data)
    {
        //Precondition: set data for CSV
        for ($i= 0; $i < count($data); $i++){
            if ($data[$i]['_email'] == '<realEmail>'){
                $data[$i]['_email'] = self::$customersEmptyData[0]['email'];
            }
            if ($data[$i]['_entity_id'] == '<realEntityID>'){
                $data[$i]['_entity_id'] = self::$customersEmptyData[$i]['address_id'];
            }
        }
        $this->admin('import');
        $this->importExportHelper()->chooseImportOptions('Customers', 'Custom Action',
            'Magento 2.0 format', 'Customer Addresses');
        //Step 5, 6, 7
        $importResult = $this->importExportHelper()->import($data);
        //Check import
        $this->assertArrayHasKey('validation', $importResult, 'Import has been finished without issues: ' .
                                 print_r($importResult));
        $this->assertArrayHasKey('error', $importResult['validation'], 'Import has been finished without issues: ' .
                                 print_r($importResult));
        $this->assertEquals(
            "Invalid value in website column in rows: 4",
            $importResult['validation']['error'][0],
            'Import has been finished with issues: ' . print_r($importResult));
        $this->assertArrayHasKey('import', $importResult, 'Import has been finished with issues: ' .
                                 print_r($importResult));
        $this->assertArrayHasKey('success', $importResult['import'], 'Import has been finished with issues: ' .
                                 print_r($importResult));
        //Verifying
        $userAddressData = $this->loadDataSet('ImportExport', 'generic_address');
        $this->admin('manage_customers');
        $this->addParameter(
            'customer_first_last_name',
            self::$customersEmptyData[0]['first_name'] . ' ' . self::$customersEmptyData[0]['last_name']);
        $this->customerHelper()->openCustomer(array('email' => self::$customersEmptyData[0]['email']));
        for ($i = 0; $i < count($data); $i++){
            $userAddressData['first_name'] = ($data[$i]['firstname'] != '') ?
                $data[$i]['firstname'] :
                self::$customersEmptyData[$i]['address']['first_name'];
            $userAddressData['last_name'] = ($data[$i]['lastname'] != '') ?
                $data[$i]['lastname'] :
                self::$customersEmptyData[$i]['address']['last_name'];
            $userAddressData['middle_name'] = ($data[$i]['middlename'] != '') ?
                $data[$i]['middlename'] :
                self::$customersEmptyData[$i]['address']['middle_name'];
            $userAddressData['city'] = ($data[$i]['city'] != '') ?
                $data[$i]['city'] :
                self::$customersEmptyData[$i]['address']['city'];
            $userAddressData['zip_code'] = ($data[$i]['postcode'] != '') ?
                $data[$i]['postcode'] :
                self::$customersEmptyData[$i]['address']['zip_code'];
            $userAddressData['telephone'] = ($data[$i]['telephone'] != '') ?
                $data[$i]['telephone'] :
                self::$customersEmptyData[$i]['address']['telephone'];
            $userAddressData['street_address_line_1'] = ($data[$i]['street'] != '') ?
                $data[$i]['street'] :
                self::$customersEmptyData[$i]['address']['street_address_line_1'];
            $userAddressData['street_address_line_2'] = ($data[$i]['street'] != '') ?
                '' :
                self::$customersEmptyData[$i]['address']['street_address_line_2'];
            $userAddressData['state'] = ($data[$i]['region'] != '') ?
                $data[$i]['region'] :
                self::$customersEmptyData[$i]['address']['state'];
            if ($data[$i]['_website'] == 'invalid'){
                $this->assertFalse((bool) $this->customerHelper()->isAddressPresent($userAddressData),
                    "Address found for address data =\n" .
                        print_r($userAddressData) .
                        "csv data =\n" .
                        print_r($data[$i]));
                $this->clearMessages();
            } else {
                $this->assertTrue((bool) $this->customerHelper()->isAddressPresent($userAddressData),
                    "Address not found for" . print_r($userAddressData) .  print_r($data[$i]));
            }
        }
    }
    /**
     * <p>Custom import: delete addresses</p>
     * <p>Verify that deleting customer address via import (custom behavior) works correctly</p>
     * <p>After steps </p>
     * <p>Verify that all Customers addresses information was deleted</p>
     *
     * @test
     * @dataProvider importDeleteData
     * @TestlinkId TL-MAGE-5687
     */
    public function deleteActionImport(array $data)
    {
        //Precondition: set data for CSV
        for ($i = 0; $i < count($data); $i++){
            if ($data[$i]['_email'] == '<realEmail>'){
                $data[$i]['_email'] = self::$customersDeleteData[0]['email'];
            }
            if ($data[$i]['_entity_id'] == '<realEntityID>'){
                $data[$i]['_entity_id'] = self::$customersDeleteData[$i]['address_id'];
            }
        }
        $this->admin('import');
        $this->importExportHelper()->chooseImportOptions('Customers', 'Custom Action',
            'Magento 2.0 format', 'Customer Addresses');
        //Step 5, 6, 7
        $importResult = $this->importExportHelper()->import($data);
        //Check import
        $this->assertArrayHasKey('validation', $importResult, 'Import has been finished without issues: ' .
                                 print_r($importResult));
        $this->assertArrayHasKey('error', $importResult['validation'], 'Import has been finished without issues: ' .
                                 print_r($importResult));
        $this->assertEquals(
            "Invalid value in website column in rows: 3",
            $importResult['validation']['error'][0],
            'Import has been finished with issues: ' . print_r($importResult));
        $this->assertArrayHasKey('import', $importResult, 'Import has been finished with issues: ' .
                                 print_r($importResult));
        $this->assertArrayHasKey('success', $importResult['import'], 'Import has been finished with issues: ' .
                                 print_r($importResult));
        //Verifying
        $this->admin('manage_customers');
        $this->addParameter(
            'customer_first_last_name',
            self::$customersDeleteData[0]['first_name'] . ' ' . self::$customersDeleteData[0]['last_name']);
        $this->customerHelper()->openCustomer(array('email' => self::$customersDeleteData[0]['email']));
        $this->assertFalse((bool) $this->customerHelper()->isAddressPresent(self::$customersDeleteData[0]['address']),
                    'Address found for: ' . print_r(self::$customersDeleteData[0]['address']));
        $this->assertFalse((bool) $this->customerHelper()->isAddressPresent(self::$customersDeleteData[1]['address']),
                    'Address found for: ' . print_r(self::$customersDeleteData[1]['address']));
        $this->assertTrue((bool) $this->customerHelper()->isAddressPresent(self::$customersDeleteData[2]['address']),
            'Address not found for: ' . print_r(self::$customersDeleteData[2]['address']));
        $this->clearMessages();
    }

    public function importUpdateData()
    {
        return array(
            array(
                array(
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array(
                            '_email' => '<realEmail>',
                            'postcode' => '10001',
                            'street' => 'ave 250',
                            'company' => '',
                            'fax' => '',
                            'middlename' => '',
                            'prefix' => '',
                            '_address_default_billing_' => '',
                            '_address_default_shipping_' => '',
                            '_action' => 'Update'
                        )
                    ),
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array(
                            '_email' => '<realEmail>',
                            'postcode' => '10002',
                            'street' => 'ave 250',
                            'company' => '',
                            'fax' => '',
                            'middlename' => '',
                            'prefix' => '',
                            '_address_default_billing_' => '',
                            '_address_default_shipping_' => '',
                            '_entity_id' => '<realEntityID>',
                            '_action' => 'UpDaTe'
                        )
                    ),
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array(
                            '_email' => '<realEmail>',
                            'postcode' => '10003',
                            'street' => 'ave 250',
                            'company' => '',
                            'fax' => '',
                            'middlename' => '',
                            'prefix' => '',
                            '_address_default_billing_' => '',
                            '_address_default_shipping_' => '',
                            '_entity_id' => '<realEntityID>',
                            '_action' => 'update'
                        )
                    ),
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array(
                            '_email' => '<realEmail>',
                            'postcode' => '10004',
                            'street' => 'ave 250',
                            'company' => '',
                            'fax' => '',
                            'middlename' => '',
                            'prefix' => '',
                            '_address_default_billing_' => '',
                            '_address_default_shipping_' => '',
                            '_entity_id' => '<realEntityID>',
                            '_action' => 'Update'
                        )
                    ),
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array(
                            '_email' => 'nonexistsemail.com',
                            'postcode' => '10005',
                            'street' => 'ave 250',
                            'company' => '',
                            'fax' => '',
                            'middlename' => '',
                            'prefix' => '',
                            '_address_default_billing_' => '',
                            '_address_default_shipping_' => '',
                            '_action' => 'Update'
                        )
                    )
                )
            )
        );
    }
    public function importEmptyData()
    {
        return array(
            array(
                array(
                    $this->loadDataSet('ImportExport','generic_address_csv',
                        array(
                            '_email' => '<realEmail>',
                            'postcode' => '10005',
                            '_entity_id' => '<realEntityID>',
                            '_action' => ''
                        )
                    ),
                    $this->loadDataSet('ImportExport','generic_address_csv',
                        array(
                            '_email' => '<realEmail>',
                            'region' => '',
                            'city' => '',
                            'country_id' => '',
                            'firstname' => '',
                            'lastname' => '',
                            'postcode' => '',
                            'street' => '',
                            'telephone' => '',
                            'company' => '',
                            'fax' => '',
                            'middlename' => '',
                            'prefix' => '',
                            '_address_default_billing_' => '',
                            '_address_default_shipping_' => '',
                            '_entity_id' => '<realEntityID>',
                            '_action' => 'Please, delete'
                        )
                    ),
                    $this->loadDataSet('ImportExport','generic_address_csv',
                        array(
                            '_email' => '<realEmail>',
                            'postcode' => '10007',
                            '_entity_id' => '',
                            '_action' => ''
                        )
                    ),
                    $this->loadDataSet('ImportExport','generic_address_csv',
                        array(
                            '_website' => 'invalid',
                            '_email' => '<realEmail>',
                            'postcode' => '10008',
                            '_entity_id' => '',
                            '_action' => 'Please, delete'
                        )
                    )
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
                        array(
                            '_email' => '<realEmail>',
                            '_entity_id' => '<realEntityID>',
                            '_action' => 'Delete'
                        )
                    ),
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array(
                            '_email' => '<realEmail>',
                            'region' => '',
                            'city' => '',
                            'country_id' => '',
                            'firstname' => '',
                            'lastname' => '',
                            'postcode' => '',
                            'street' => '',
                            'telephone' => '',
                            'company' => '',
                            'fax' => '',
                            'middlename' => '',
                            'prefix' => '',
                            '_address_default_billing_' => '',
                            '_address_default_shipping_' => '',
                            '_entity_id' => '<realEntityID>',
                            '_action' => 'delete'
                        )
                    ),
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array(
                            '_website' => 'invalid',
                            '_email' => '<realEmail>',
                            '_entity_id' => '<realEntityID>',
                            '_action' => 'delete'
                        )
                    ),
                    $this->loadDataSet('ImportExport', 'generic_address_csv',
                        array(
                            '_email' => '<realEmail>',
                            '_entity_id' => '',
                            '_action' => 'delete'
                        )
                    )
                )
            )
        );
    }
}