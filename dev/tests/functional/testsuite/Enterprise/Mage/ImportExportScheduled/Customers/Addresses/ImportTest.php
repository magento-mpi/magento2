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
 * Customer Backward Compatibility Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_ImportExportScheduled_Customers_Addresses_ImportTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->markTestIncomplete('MAGETWO-11477');
    }

    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        $this->navigate('scheduled_import_export');
    }

    /**
     * Simple Scheduled Export Precondition
     *
     * @test
     */
    public function preconditionImport()
    {
        $this->navigate('manage_customers');
        $customerData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($customerData);
        return $customerData;
    }

    /**
     * Create customer addresses and return array of Ids
     *
     * @param $addressData
     * @param $customerData
     * @param $addressCsv
     *
     * @return array
     */
    protected function _createAddress($addressData, $customerData, array $addressCsv)
    {
        //Precondition: create addresses if needed
        foreach ($addressData as $key => $value) {
            if ($value) {
                $this->navigate('manage_customers');
                $this->customerHelper()->openCustomer(array('email' => $customerData['email']));
                $this->openTab('addresses');
                if ($this->customerHelper()->isAddressPresent($value) == 0) {
                    $this->customerHelper()->addAddress($value);
                    $this->saveForm('save_customer');
                    $this->assertMessagePresent('success', 'success_saved_customer');
                    $this->customerHelper()->openCustomer(array('email' => $customerData['email']));
                    $this->openTab('addresses');
                }
                ;
                $addressCsv[$key]['_entity_id'] = $this->customerHelper()->isAddressPresent($value);
            }
        }
        return $addressCsv;
    }

    /**
     * Running Scheduled Import of Customer Addresses File (Add/Update, Delete Entities, Custom Action)
     * Precondition: one customer with address is created.
     *
     * @dataProvider addressImportData
     * @depends preconditionImport
     * @test
     * @testLinkId TL-MAGE-5789, TL-MAGE-5792, TL-MAGE-5795
     */
    public function importValidData($originalAddressData, $addressCsv, $behavior, $newAddressData, $customerData)
    {
        $addressCsv = $this->_createAddress($originalAddressData, $customerData, $addressCsv);
        //set correct email and address id to csv data
        for ($i = 0; $i < count($addressCsv); $i++) {
            $addressCsv[$i] = str_replace('<realEmail>', $customerData['email'], $addressCsv[$i]);
        }
        //Precondition: create scheduled import
        $importData = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'entity_type' => 'Customer Addresses',
            'behavior' => $behavior,
            'file_name' => date('Y-m-d_H-i-s_') . 'export_customer_address.csv',
        ));
        $this->importExportScheduledHelper()->putCsvToFtp($importData, $addressCsv);
        $this->navigate('scheduled_import_export');
        $this->importExportScheduledHelper()->createImport($importData);
        $this->assertMessagePresent('success', 'success_saved_import');
        //Steps 1-2
        $this->importExportScheduledHelper()->applyAction(
            array(
                'name' => $importData['name'],
                'operation' => 'Import'
            )
        );
        //Verifying
        $this->assertMessagePresent('success', 'success_run');
        $this->assertEquals('Successful',
            $this->importExportScheduledHelper()->getLastOutcome(
                array(
                    'name' => $importData['name'],
                    'operation' => 'Import'
                )
            ), 'Error is occurred');
        //Step 3
        $this->navigate('manage_customers');
        //Step 4
        $this->customerHelper()->openCustomer(array('email' => $customerData['email']));
        $this->openTab('addresses');
        //Verifying
        foreach ($newAddressData as $key => $value) {
            if ((isset($addressCsv[$key]['_action']) && strtolower($addressCsv[$key]['_action']) == 'delete')
                || $behavior == 'Delete Entities'
            ) {
                $this->assertEquals(0, $this->customerHelper()->isAddressPresent($value),
                    'Customer address is found');
            } else {
                $this->assertNotEquals(0, $this->customerHelper()->isAddressPresent($value),
                    'Customer address is not found');
                if ($addressCsv[$key]['_entity_id'] != '') {
                    $this->assertEquals($addressCsv[$key]['_entity_id'],
                        $this->customerHelper()->isAddressPresent($value), 'Customer address has not been updated');
                }
            }
        }
    }

    /**
     * Data provider for importValidData
     *
     * @return array
     */
    public function addressImportData()
    {
        $originalAddressData = array();
        $newAddressData = array();
        $csvFile = array();
        $originalAddressData[1] = include __DIR__ . '/_files/row_original_address_data_array_1.php';
        $csvFile[1]             = include __DIR__ . '/_files/row_csv_file_array_1.php';
        $newAddressData[1]      = include __DIR__ . '/_files/row_new_address_data_array_1.php';
        $originalAddressData[2] = include __DIR__ . '/_files/row_original_address_data_array_2.php';
        $csvFile[2]             = include __DIR__ . '/_files/row_csv_file_array_2.php';
        $newAddressData[2] = $originalAddressData[2];
        $originalAddressData[3] = include __DIR__ . '/_files/row_original_address_data_array_3.php';
        $csvFile[3]             = include __DIR__ . '/_files/row_csv_file_array_3.php';
        $newAddressData[3]      = include __DIR__ . '/_files/row_new_address_data_array_3.php';
        return array(
            array($originalAddressData[1], $csvFile[1], 'Add/Update Complex Data', $newAddressData[1]),
            array($originalAddressData[2], $csvFile[2], 'Delete Entities', $newAddressData[2]),
            array($originalAddressData[3], $csvFile[3], 'Custom Action', $newAddressData[3]),
        );
    }

    /**
     * Invalid data in Customer Addresses File
     *
     * @dataProvider addressInvalidImportData
     * @depends preconditionImport
     * @test
     * @testLinkId TL-MAGE-5800
     */
    public function importInvalidData($addressCsv, $customerData)
    {
        //set correct email and address id to csv data
        foreach ($addressCsv as $key => $value) {
            $addressCsv[$key] = str_replace('<realEmail>', $customerData['email'], $value);
        }
        //Precondition: create scheduled import
        $importData = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'entity_type' => 'Customer Addresses',
            'behavior' => 'Add/Update Complex Data',
            'file_name' => date('Y-m-d_H-i-s_') . 'export_customer_address.csv',
        ));
        $this->importExportScheduledHelper()->putCsvToFtp($importData, $addressCsv);
        $this->importExportScheduledHelper()->createImport($importData);
        $this->assertMessagePresent('success', 'success_saved_import');
        //Steps 1-2
        $this->importExportScheduledHelper()->applyAction(
            array(
                'name' => $importData['name'],
                'operation' => 'Import'
            )
        );
        //Verifying
        $this->assertMessagePresent('error', 'error_run');
        $this->assertEquals('Failed',
            $this->importExportScheduledHelper()->getLastOutcome(
                array(
                    'name' => $importData['name'],
                    'operation' => 'Import'
                )
            ), 'Error is occurred');
    }

    public function addressInvalidImportData()
    {
        $csvFile = array(
            $this->loadDataSet('ImportExport', 'generic_address_csv', array(
                    '_entity_id' => '',
                    '_email' => '<realEmail>',
                    'city' => 'Kingsport',
                    'company' => 'Weingarten\'s',
                    'fax' => '423-389-1069',
                    'firstname' => 'Linda',
                    'lastname' => 'Gilbert',
                    'middlename' => 'S.',
                    'postcode' => '37663',
                    'region' => 'Tennessee',
                    'street' => '1596 Public Works Drive',
                    'telephone' => '423-389-1069',
                )
            ),
            $this->loadDataSet('ImportExport', 'generic_address_csv', array(
                    '_entity_id' => '',
                    '_email' => '<realEmail>',
                    '_website' => 'invalid',
                    'city' => 'Memphis',
                    'company' => 'Omni Source',
                    'fax' => '662-404-3860',
                    'firstname' => 'Keith',
                    'lastname' => 'Cox',
                    'middlename' => 'T.',
                    'postcode' => '38133',
                    'region' => 'Mississippi',
                    'street' => '2774 Brownton Road',
                    'telephone' => '662-404-3860',
                )
            ),
        );
        return array(
            array($csvFile),
        );
    }
}
