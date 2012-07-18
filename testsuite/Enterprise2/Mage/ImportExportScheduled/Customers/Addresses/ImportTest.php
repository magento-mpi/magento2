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
 * @method Enterprise2_Mage_ImportExportScheduled_Helper  importExportScheduledHelper() importExportScheduledHelper()
 */
class Enterprise2_Mage_ImportExportScheduled_Import_AddressesTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        $this->navigate('scheduled_import_export');
    }

    /**
     * <p>Simple Scheduled Export Precondition</p>
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
     * <p>Running Scheduled Import of Customer Addresses File (Add/Update, Delete Entities, Custom Action)</p>
     * <p>Precondition: one customer with address is created.</p>
     * <p>Steps:</p>
     * <p>1. In System > Import/Export > Scheduled Import/Export select check box for Scheduled Import</p>
     * <p>2. In "Actions" drop-down select "Run"</p>
     * <p>Expected: last Outcome of run Scheduled Import changes from Pending to Successful. </p>
     * <p>Message “Operation has been successfully run.” in green frame should appeared.</p>
     * <p>3. Open Customers -> Manage Customers</p>
     * <p>4. Open customer from precondition</p>
     * <p>Expected: customers address information was imported</p>
     *
     * @dataProvider addressImportData
     * @depends preconditionImport
     * @test
     * @testLinkId TL-MAGE-5789, TL-MAGE-5792, TL-MAGE-5795
     */
    public function importValidData($originalAddressData, $addressCsv, $behavior, $newAddressData, $customerData)
    {
        //Precondition: create addresses if needed
        foreach ($originalAddressData as $key => $value) {
            if ($value) {
                $this->navigate('manage_customers');
                $this->addParameter('customer_first_last_name',
                    $customerData['first_name'] . ' ' . $customerData['last_name']);
                $this->customerHelper()->openCustomer(array('email' => $customerData['email']));
                $this->openTab('addresses');
                if ($this->customerHelper()->isAddressPresent($value) == 0) {
                    $this->customerHelper()->addAddress($value);
                    $this->customerHelper()->saveForm('save_customer');
                    $this->assertMessagePresent('success', 'success_saved_customer');
                    $this->customerHelper()->openCustomer(array('email' => $customerData['email']));
                    $this->openTab('addresses');
                };
                $addressCsv[$key]['_entity_id'] = $this->customerHelper()->isAddressPresent($value);
            }
        }
        //set correct email and address id to csv data
        foreach ($addressCsv as $key => $value) {
            $addressCsv[$key] = str_replace('<realEmail>', $customerData['email'], $addressCsv[$key]);
        }
        //Precondition: create scheduled import
        $importData = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'file_format_version' => 'Magento 2.0 format',
            'entity_subtype' => 'Customer Addresses',
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
                || $behavior == 'Delete Entities') {
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

    public function addressImportData()
    {
        $originalAddressData1 = array(
            $this->loadDataSet('Customers', 'generic_address', array(
                        'city' => 'Washington',
                        'company' => 'Sound Warehouse',
                        'fax' => '586-786-9753',
                        'first_name' => 'Thomas',
                        'last_name' => 'Keeney',
                        'middle_name' => 'A.',
                        'zip_code' => '48094',
                        'state' => 'Michigan',
                        'street_address_line_1' => '3245 Ritter Avenue',
                        'street_address_line_2' => '',
                        'telephone' => '586-786-9753',
                )
            ), null
        );
        $csvFile1 = array(
            $this->loadDataSet('ImportExport', 'generic_address_csv', array(
                    '_entity_id' => '',
                    '_email' => '<realEmail>',
                    'city' => 'Harrisburg',
                    'company' => 'The Flying Bear',
                    'fax' => '717-503-8908',
                    'firstname' => 'Ronald',
                    'lastname' => 'Armstrong',
                    'middlename' => 'M.',
                    'postcode' => '17111',
                    'region' => 'Pennsylvania',
                    'street' => '154 Saint James Drive',
                    'telephone' => '717-503-8908',
                )
            ),
            $this->loadDataSet('ImportExport', 'generic_address_csv', array(
                    '_entity_id' => '',
                    '_email' => '<realEmail>',
                    'city' => 'Boston',
                    'company' => 'Quality Event Planner',
                    'fax' => '617-956-7518',
                    'firstname' => 'Jeremy',
                    'lastname' => 'Bradbury',
                    'middlename' => 'D.',
                    'postcode' => '02109',
                    'region' => 'Massachusetts',
                    'street' => '2524 Rainy Day Drive',
                    'telephone' => '617-956-7518',
                )
            ),
        );
        $newAddressData1 = array(
            $this->loadDataSet('Customers', 'generic_address', array(
                    'city' => 'Harrisburg',
                    'company' => 'The Flying Bear',
                    'fax' => '717-503-8908',
                    'first_name' => 'Ronald',
                    'last_name' => 'Armstrong',
                    'middle_name' => 'M.',
                    'zip_code' => '17111',
                    'state' => 'Pennsylvania',
                    'street_address_line_1' => '154 Saint James Drive',
                    'street_address_line_2' => '',
                    'telephone' => '717-503-8908',
                )
            ),
            $this->loadDataSet('Customers', 'generic_address', array(
                    'city' => 'Boston',
                    'company' => 'Quality Event Planner',
                    'fax' => '617-956-7518',
                    'first_name' => 'Jeremy',
                    'last_name' => 'Bradbury',
                    'middle_name' => 'D.',
                    'zip_code' => '02109',
                    'state' => 'Massachusetts',
                    'street_address_line_1' => '2524 Rainy Day Drive',
                    'street_address_line_2' => '',
                    'telephone' => '617-956-7518',
                )
            ),
        );
        $originalAddressData2 = array(
            $this->loadDataSet('Customers', 'generic_address', array(
                    'city' => 'Harrisburg',
                    'company' => 'The Flying Bear',
                    'fax' => '717-503-8908',
                    'first_name' => 'Ronald',
                    'last_name' => 'Armstrong',
                    'middle_name' => 'M.',
                    'zip_code' => '17111',
                    'state' => 'Pennsylvania',
                    'street_address_line_1' => '154 Saint James Drive',
                    'street_address_line_2' => '',
                    'telephone' => '717-503-8908',
                )
            ),
            $this->loadDataSet('Customers', 'generic_address', array(
                    'city' => 'Boston',
                    'company' => 'Quality Event Planner',
                    'fax' => '617-956-7518',
                    'first_name' => 'Jeremy',
                    'last_name' => 'Bradbury',
                    'middle_name' => 'D.',
                    'zip_code' => '02109',
                    'state' => 'Massachusetts',
                    'street_address_line_1' => '2524 Rainy Day Drive',
                    'street_address_line_2' => '',
                    'telephone' => '617-956-7518',
                )
            ),
        );
        $csvFile2 = array(
            $this->loadDataSet('ImportExport', 'generic_address_csv', array(
                    '_entity_id' => '',
                    '_email' => '<realEmail>',
                    'city' => 'Harrisburg',
                    'company' => 'The Flying Bear',
                    'fax' => '717-503-8908',
                    'firstname' => 'Ronald',
                    'lastname' => 'Armstrong',
                    'middlename' => 'M.',
                    'postcode' => '17111',
                    'region' => 'Pennsylvania',
                    'street' => '154 Saint James Drive',
                    'telephone' => '717-503-8908',
                )
            ),
            $this->loadDataSet('ImportExport', 'generic_address_csv', array(
                    '_entity_id' => '',
                    '_email' => '<realEmail>',
                    'city' => 'Cambridge',
                    'company' => 'Best Biz Survis',
                    'fax' => '781-210-5960',
                    'firstname' => 'Arvilla',
                    'lastname' => 'Hubbs',
                    'middlename' => 'P.',
                    'postcode' => '02142',
                    'region' => 'Massachusetts',
                    'street' => '3862 Wescam Court',
                    'telephone' => '781-210-5960',
                )
            ),
        );
        $newAddressData2 = $originalAddressData2;
        $originalAddressData3 = array(
            $this->loadDataSet('Customers', 'generic_address', array(
                    'city' => 'Chattanooga',
                    'company' => 'Hit or Miss',
                    'fax' => '423-313-8300',
                    'first_name' => 'Maureen',
                    'last_name' => 'Velez',
                    'middle_name' => 'G.',
                    'zip_code' => '37408',
                    'state' => 'Tennessee',
                    'street_address_line_1' => '3059 Public Works Drive',
                    'street_address_line_2' => '',
                    'telephone' => '423-313-8300',
                )
            ),
            $this->loadDataSet('Customers', 'generic_address', array(
                    'city' => 'Baltimore',
                    'company' => 'Strength Gurus',
                    'fax' => '443-337-8871',
                    'first_name' => 'Henry',
                    'last_name' => 'Page',
                    'middle_name' => 'K.',
                    'zip_code' => '21202',
                    'state' => 'Maryland',
                    'street_address_line_1' => '2715 Calvin Street',
                    'street_address_line_2' => '',
                    'telephone' => '443-337-8871',
                )
            ), null
        );
        $csvFile3 = array(
            $this->loadDataSet('ImportExport', 'generic_address_csv', array(
                    '_entity_id' => '',
                    '_email' => '<realEmail>',
                    '_action' => 'delete',
                    'city' => 'Chattanooga',
                    'company' => 'Hit or Miss',
                    'fax' => '423-313-8300',
                    'firstname' => 'Maureen',
                    'lastname' => 'Velez',
                    'middlename' => 'M.',
                    'postcode' => '17111',
                    'region' => 'Pennsylvania',
                    'street' => '154 Saint James Drive',
                    'telephone' => '423-313-8300',
                )
            ),
            $this->loadDataSet('ImportExport', 'generic_address_csv', array(
                    '_entity_id' => '',
                    '_email' => '<realEmail>',
                    '_action' => 'update',
                    'city' => 'Stockbridge',
                    'company' => 'White Tower Hamburgers',
                    'fax' => '678-565-2507',
                    'firstname' => 'Lisa',
                    'lastname' => 'Lewis',
                    'middlename' => 'M.',
                    'postcode' => '30281',
                    'region' => 'Georgia',
                    'street' => '3292 Hanifan Lane',
                    'telephone' => '678-565-2507',
                )
            ),
            $this->loadDataSet('ImportExport', 'generic_address_csv', array(
                    '_entity_id' => '',
                    '_email' => '<realEmail>',
                    'city' => 'San Diego',
                    'company' => 'Security Sporting Goods',
                    'fax' => '619-696-3735',
                    'firstname' => 'Luis',
                    'lastname' => 'Meade',
                    'middlename' => 'J.',
                    'postcode' => '92101',
                    'region' => 'California',
                    'street' => '1776 Grim Avenue',
                    'telephone' => '619-696-3735',
                )
            ),
        );
        $newAddressData3 = array(
            $this->loadDataSet('Customers', 'generic_address', array(
                    'city' => 'Chattanooga',
                    'company' => 'Hit or Miss',
                    'fax' => '423-313-8300',
                    'first_name' => 'Maureen',
                    'last_name' => 'Velez',
                    'middle_name' => 'G.',
                    'zip_code' => '37408',
                    'state' => 'Tennessee',
                    'street_address_line_1' => '3059 Public Works Drive',
                    'street_address_line_2' => '',
                    'telephone' => '423-313-8300',
                )
            ),
            $this->loadDataSet('Customers', 'generic_address', array(
                    'city' => 'Stockbridge',
                    'company' => 'White Tower Hamburgers',
                    'fax' => '678-565-2507',
                    'first_name' => 'Lisa',
                    'last_name' => 'Lewis',
                    'middle_name' => 'M.',
                    'zip_code' => '30281',
                    'state' => 'Georgia',
                    'street_address_line_1' => '3292 Hanifan Lane',
                    'street_address_line_2' => '',
                    'telephone' => '678-565-2507',
                )
            ),
            $this->loadDataSet('Customers', 'generic_address', array(
                    'city' => 'San Diego',
                    'company' => 'Security Sporting Goods',
                    'fax' => '619-696-3735',
                    'first_name' => 'Luis',
                    'last_name' => 'Meade',
                    'middle_name' => 'J.',
                    'zip_code' => '92101',
                    'state' => 'California',
                    'street_address_line_1' => '1776 Grim Avenue',
                    'street_address_line_2' => '',
                    'telephone' => '619-696-3735',
                )
            ),
        );
        return array(
            array($originalAddressData1, $csvFile1, 'Add/Update Complex Data', $newAddressData1),
            array($originalAddressData2, $csvFile2, 'Delete Entities', $newAddressData2),
            array($originalAddressData3, $csvFile3, 'Custom Action', $newAddressData3),
        );
    }
}