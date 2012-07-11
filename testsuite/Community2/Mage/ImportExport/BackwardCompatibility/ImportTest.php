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
class Community2_Mage_ImportExport_Backward_Import_CustomerTest extends Mage_Selenium_TestCase
{
    protected static $customerData = array();
    protected static $addressData = array();

    /**
     * <p>Precondition:</p>
     * <p>Create new customer</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        self::$customerData = $this->loadDataSet('Customers', 'generic_customer_account');
        self::$addressData = $this->loadDataSet('Customers', 'generic_address');
        $this->customerHelper()->createCustomer(self::$customerData, self::$addressData);
        $this->assertMessagePresent('success', 'success_saved_customer');
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
     * <p>Validation Result block</p>
     * <p>Verify that Validation Result block will be displayed after checking data of import file</p>
     * <p>Precondition: at least one customer exists, one file is generated after export</p>
     * <p>Steps</p>
     * <p>1. Go to System -> Import/ Export -> Import</p>
     * <p>2. In the drop-down "Entity Type" select "Customers"</p>
     * <p>3. In "Import Format Version" dropdown field choose "Magento 1.7 format" parameter</p>
     * <p>4. In "Import Behavior" dropdown field choose "Append Complex Data" parameter</p>
     * <p>5. Select file to import</p>
     * <p>6. Click "Check Data" button.</p>
     * <p>Expected: validation and success messages are correct</p>
     *
     * @test
     * @TestlinkId TL-MAGE-1108
     */
    public function validationResultBlock()
    {
        //Precondition
        $this->navigate('export');
        $this->importExportHelper()->chooseExportOptions('Customers', 'Magento 1.7 format');
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
        $this->importExportHelper()->chooseImportOptions('Customers', 'Append Complex Data',
            'Magento 1.7 format');
        //Step 5-6
        $importData = $this->importExportHelper()->import($report);
        //Verifying
        $this->assertEquals('Checked rows: ' . count($report) . ', checked entities: '
                . $numberOfEntities
                . ', invalid rows: 0, total errors: 0', $importData['validation']['validation'][0],
            'Validation message is not correct');
        $this->assertEquals('File is valid! To start import process press "Import" button  Import',
            $importData['validation']['success'][0], 'Success message is not correct');
    }

    /**
     * <p>Required columns</p>
     * <p>Precond
     * <p>Steps</p>
     * <p>1. Go to System -> Import / Export -> Import</p>
     * <p>2. Select Entity Type: Customers</p>
     * <p>3. Select Export Format Version: Magento 1.7 format</p>
     * <p>4. In Import Behavior dropdown field choose Append Complex Data parameter</p>
     * <p>5. Choose file from precondition</p>
     * <p>6. Click on Check Data</p>
     * <p>7. Click on Import button</p>
     * <p>8. Open Customers -> Manage Customers</p>
     * <p>9. Open each of imported customers</p>
     * <p>Expected: </p>
     * <p>After step 6</p>
     * <p>Verify that file is valid, the message 'File is valid!' is displayed</p>
     * <p>After step 7</p>
     * <p>Verify that import starting. The message 'Import successfully done.' is displayed</p>
     * <p>After step 8</p>
     * <p>Verify that imported customers display on customers grid</p>
     * <p>After step 9</p>
     * <p>Verify that all Customer information was imported</p>
     *
     * @test
     * @dataProvider importWithRequiredColumnsData
     * @TestlinkId TL-MAGE-1167
     */
    public function importWithRequiredColumns($data)
    {
        //Set email for existing customer
        $data[0]['email'] = self::$customerData['email'];
        //Steps 2-4
        $this->importExportHelper()->chooseImportOptions('Customers', 'Append Complex Data',
            'Magento 1.7 format');
        //Steps 5-7
        $report = $this->importExportHelper()->import($data);
        //Verify import
        $this->assertArrayHasKey('import', $report, 'Import has been finished with issues:' .
            print_r($report) . print_r($data));
        $this->assertArrayHasKey('success', $report['import'], 'Import has been finished with issues:' .
            print_r($report) . print_r($data));
        //Check updated customer
        self::$customerData['first_name'] = $data[0]['firstname'];
        self::$customerData['last_name'] = $data[0]['lastname'];
        $this->navigate('manage_customers');
        $this->addParameter('customer_first_last_name',
            self::$customerData['first_name'] . ' ' . self::$customerData['last_name']);
        $this->customerHelper()->openCustomer(
            array(
                'email' => self::$customerData['email'])
            );
        $this->assertTrue($this->verifyForm(self::$customerData, 'account_information'),
            'Customer has not been updated');
        //Check new customer
        $customerData = $this->loadDataSet('Customers', 'generic_customer_account', array(
            'email' => $data[1]['email'],
            'first_name' => $data[1]['firstname'],
            'last_name' => $data[1]['lastname'],
        ));
        $this->navigate('manage_customers');
        $this->assertTrue($this->customerHelper()->isCustomerPresentInGrid($customerData),
            'Customer has not been created');
        $this->addParameter('customer_first_last_name', $customerData['first_name'] . ' ' . $customerData['last_name']);
        $this->customerHelper()->openCustomer(
            array(
                'email' => strtolower($customerData['email'])
            ));
    }

    public function importWithRequiredColumnsData()
    {
        return array(
            array(
                array($this->loadDataSet('ImportExport', 'generic_customer_csv', array(
                    'firstname' => 'New First Name',
                    'lastname' => 'New Last Name',
                    '_store' => '',
                )),
                    $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
                    '_store' => '',
                )),
            )
        ));
    }
 }