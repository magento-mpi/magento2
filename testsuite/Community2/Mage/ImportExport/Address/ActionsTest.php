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
class Enterprise2_Mage_ImportExport_CustomActionsImportAddressTest extends Mage_Selenium_TestCase
{
    static protected $customersData = array();
    public function setUpBeforeTests(){
        //logged in once for all tests
        $this->loginAdminUser();
        for($i = 0; $i<2; $i++){
            $this->admin('manage_customers');
            $userData = $this->loadDataSet('ImportExport', 'generic_customer_account');
            $userAddressData = $this->loadDataSet('ImportExport', 'generic_address');
            $this->customerHelper()->createCustomer($userData, $userAddressData);
            $this->assertMessagePresent('success', 'success_saved_customer');
            $this->addParameter(
                'customer_first_last_name',
                $userData['first_name'] . ' ' . $userData['last_name']);
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            self::$customersData[] = array('email' => $userData['email'],
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'address' => $this->customerHelper()->isAddressPresent($userAddressData)
            );
        }
        for($i = 2; $i<4; $i++){
            $this->admin('manage_customers');
            $userData = $this->loadDataSet('ImportExport', 'generic_customer_account');
            $userAddressData = $this->loadDataSet('ImportExport', 'generic_address');
            $userAddressData1 = $this->loadDataSet('ImportExport', 'generic_address');
            $this->customerHelper()->createCustomer($userData, $userAddressData);
            $this->assertMessagePresent('success', 'success_saved_customer');
            $this->addParameter(
                'customer_first_last_name',
                $userData['first_name'] . ' ' . $userData['last_name']);
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            $this->customerHelper()->addAddress($userAddressData1);
            $this->customerHelper()->saveForm('save_customer');
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            self::$customersData[] = array('email' => $userData['email'],
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'address' => $this->customerHelper()->isAddressPresent($userAddressData)
            );
            self::$customersData[] = array('email' => $userData['email'],
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'address' => $this->customerHelper()->isAddressPresent($userAddressData1)
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
        $this->navigate('export');
    }

    /**
     * <p>Custom import: update finance information</p>
     * <p>Need to verify that the customer finances information is updated if the action is "Update" in the csv file</p>
     * <p>After steps </p>
     * <p>Verify that all Customers finance information was imported</p>
     *
     * @test
     * @dataProvider importUpdateData
     * @TestlinkId TL-MAGE-5689
     */
    public function updateActionImport(array $data)
    {
        //Precondition: set data for CSV
        $i = 0;
        foreach($data as $customerData){
            if ($data[$i]['_email'] == '%realEmail%'){
                $data[$i]['_email'] = self::$customersData[$i]['email'];
            }
            if ($data[$i]['_entity_id'] == '%realEntityID%'){
                $data[$i]['_entity_id'] = self::$customersData[$i]['address'];
            }
            $i++;
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
        for($i = 0; $i < 4; $i++){
             $this->admin('manage_customers');
             $this->addParameter(
                 'customer_first_last_name',
                 self::$customersData[$i]['first_name'] . ' ' . self::$customersData[$i]['last_name']
             );
             $this->customerHelper()->openCustomer(array('email' => self::$customersData[$i]['email']));
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
            $this->assertFalse(!$this->customerHelper()->isAddressPresent($userAddressData),
                             'Address not found for: ' . print_r($userAddressData));
        }
    }
    /**
     * <p>Custom import: update finance information</p>
     * <p>Need to verify that the customer finances information is updated if the action is "Update" in the csv file</p>
     * <p>After steps </p>
     * <p>Verify that all Customers finance information was imported</p>
     *
     * @test
     * @dataProvider importEmptyData
     * @TestlinkId TL-MAGE-5688
     */
    public function emptyActionImport(array $data)
    {
        //Precondition: set data for CSV
        $i = 0;
        foreach($data as $customerData){
            if ($data[$i]['_email'] == '%realEmail%'){
                $data[$i]['_email'] = self::$customersData[4]['email'];
            }
            if ($data[$i]['_entity_id'] == '%realEntityID%'){
                $data[$i]['_entity_id'] = self::$customersData[4+$i]['address'];
            }
            $i++;
        }
        $this->admin('import');
        $this->importExportHelper()->chooseImportOptions('Customers', 'Add/Update Complex Data',
            'Magento 2.0 format', 'Customer Addresses');
        //Step 5, 6, 7
        $importResult = $this->importExportHelper()->import($data);
        //Check import
        $this->assertArrayHasKey('validation', $importResult, 'Import has been finished without issues: '
            . print_r($importResult));
        $this->assertArrayHasKey('error', $importResult['validation'], 'Import has been finished without issues: '
            . print_r($importResult));
        $this->assertEquals(
            "Invalid value in Website column (website does not exists?) in rows: 4",
            $importResult['validation']['error'][0],
            'Import has been finished with issues: ' . print_r($importResult));
        $this->assertArrayHasKey('import', $importResult, 'Import has been finished with issues: '
            . print_r($importResult));
        $this->assertArrayHasKey('success', $importResult['import'], 'Import has been finished with issues: '
            . print_r($importResult));
        //Verifying
        $userAddressData = $this->loadDataSet('ImportExport', 'generic_address');
        foreach($data as $customerData){
            $this->admin('manage_customers');
            $this->addParameter(
                'customer_first_last_name',
                self::$customersData[4]['first_name'] . ' ' . self::$customersData[4]['last_name']);
            $this->customerHelper()->openCustomer(array('email' => self::$customersData[4]['email']));
            $userAddressData['first_name'] = ($customerData['firstname']!='')?$customerData['firstname']:$userAddressData['first_name'];
            $userAddressData['last_name'] = ($customerData['lastname']!='')?$customerData['lastname']:$userAddressData['last_name'];
            $userAddressData['city'] = ($customerData['city']!='')?$customerData['city']:$userAddressData['city'];
            $userAddressData['zip_code'] = ($customerData['postcode']!='')?$customerData['postcode']:$userAddressData['zip_code'];
            $userAddressData['telephone'] = ($customerData['telephone']!='')?$customerData['telephone']:$userAddressData['telephone'];
            $userAddressData['street_address_line_1'] = ($customerData['street']!='') ? $customerData['street']:$userAddressData['street_address_line_1'];
            $userAddressData['street_address_line_2'] = '';
            $userAddressData['state'] = ($customerData['region']!='')? $customerData['region']:$userAddressData['state'];
            if ($customerData['_website'] == 'invalid'){
                $this->assertTrue(!$this->customerHelper()->isAddressPresent($userAddressData),
                    'Address found for: ' . print_r($userAddressData));
                $this->clearMessages();
            } else {
                $this->assertFalse(!$this->customerHelper()->isAddressPresent($userAddressData),
                    'Address not found for: ' . print_r($userAddressData));
            }
        }
    }

    public function importUpdateData()
    {
        return array(
            array(
                array(
                    array(
                        '_website' => 'base',
                        '_email' => '%realEmail%',
                        'region' => 'New York',
                        'city' => 'New York',
                        'country_id' => 'US',
                        'firstname' => 'John',
                        'lastname' => 'Nothing',
                        'postcode' => '10001',
                        'street' => 'ave 250',
                        'telephone' => '1010101',
                        'company' => '',
                        'fax' => '',
                        'middlename' => '',
                        'prefix' => '',
                        '_address_default_billing_' => '',
                        '_address_default_shipping_' => '',
                        '_entity_id' => '%randomize%',
                        'action' => 'Update'
                    ),
                    array(
                        '_website' => 'base',
                        '_email' => '%realEmail%',
                        'region' => 'New York',
                        'city' => 'New York',
                        'country_id' => 'US',
                        'firstname' => 'John',
                        'lastname' => 'Nothing',
                        'postcode' => '10002',
                        'street' => 'ave 250',
                        'telephone' => '1010101',
                        'company' => '',
                        'fax' => '',
                        'middlename' => '',
                        'prefix' => '',
                        '_address_default_billing_' => '',
                        '_address_default_shipping_' => '',
                        '_entity_id' => '%realEntityID%',
                        'action' => 'UpDaTe'
                    ),
                    array(
                        '_website' => 'base',
                        '_email' => '%realEmail%',
                        'region' => 'New York',
                        'city' => 'New York',
                        'country_id' => 'US',
                        'firstname' => 'John',
                        'lastname' => 'Nothing',
                        'postcode' => '10003',
                        'street' => 'ave 250',
                        'telephone' => '1010101',
                        'company' => '',
                        'fax' => '',
                        'middlename' => '',
                        'prefix' => '',
                        '_address_default_billing_' => '',
                        '_address_default_shipping_' => '',
                        '_entity_id' => '%realEntityID%',
                        'action' => 'update'
                    ),
                    array(
                        '_website' => 'base',
                        '_email' => '%realEmail%',
                        'region' => 'New York',
                        'city' => 'New York',
                        'country_id' => 'US',
                        'firstname' => 'John',
                        'lastname' => 'Nothing',
                        'postcode' => '10004',
                        'street' => 'ave 250',
                        'telephone' => '1010101',
                        'company' => '',
                        'fax' => '',
                        'middlename' => '',
                        'prefix' => '',
                        '_address_default_billing_' => '',
                        '_address_default_shipping_' => '',
                        '_entity_id' => '%realEntityID%',
                        'action' => 'Update'
                    ),
                    array(
                        '_website' => 'base',
                        '_email' => 'nonexistsemail.com',
                        'region' => 'New York',
                        'city' => 'New York',
                        'country_id' => 'US',
                        'firstname' => 'John',
                        'lastname' => 'Nothing',
                        'postcode' => '10005',
                        'street' => 'ave 250',
                        'telephone' => '1010101',
                        'company' => '',
                        'fax' => '',
                        'middlename' => '',
                        'prefix' => '',
                        '_address_default_billing_' => '',
                        '_address_default_shipping_' => '',
                        '_entity_id' => '1234',
                        'action' => 'Update'
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
                    array(
                        '_website' => 'base',
                        '_email' => '%realEmail%',
                        'region' => 'New York',
                        'city' => 'New York',
                        'country_id' => 'US',
                        'firstname' => 'John',
                        'lastname' => 'Nothing',
                        'postcode' => '10005',
                        'street' => 'ave 250',
                        'telephone' => '1010101',
                        'company' => "Earl Abel's",
                        'fax' => '',
                        'middlename' => '',
                        'prefix' => '',
                        '_address_default_billing_' => '',
                        '_address_default_shipping_' => '',
                        '_entity_id' => '%realEntityID%',
                        'action' => ''
                    ),
                    array(
                        '_website' => 'base',
                        '_email' => '%realEmail%',
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
                        '_entity_id' => '%realEntityID%',
                        'action' => 'Please, delete'
                    ),
                    array(
                        '_website' => 'base',
                        '_email' => '%realEmail%',
                        'region' => 'New York',
                        'city' => 'New York',
                        'country_id' => 'US',
                        'firstname' => 'John',
                        'lastname' => 'Nothing',
                        'postcode' => '10007',
                        'street' => 'ave 250',
                        'telephone' => '1010101',
                        'company' => "Earl Abel's",
                        'fax' => '',
                        'middlename' => '',
                        'prefix' => '',
                        '_address_default_billing_' => '',
                        '_address_default_shipping_' => '',
                        '_entity_id' => '',
                        'action' => ''
                    ),
                    array(
                        '_website' => 'invalid',
                        '_email' => '%realEmail%',
                        'region' => 'New York',
                        'city' => 'New York',
                        'country_id' => 'US',
                        'firstname' => 'John',
                        'lastname' => 'Nothing',
                        'postcode' => '10008',
                        'street' => 'ave 250',
                        'telephone' => '1010101',
                        'company' => "Earl Abel's",
                        'fax' => '',
                        'middlename' => '',
                        'prefix' => '',
                        '_address_default_billing_' => '',
                        '_address_default_shipping_' => '',
                        '_entity_id' => '',
                        'action' => 'Please, delete'
                    )
                )
            )
        );
    }
}