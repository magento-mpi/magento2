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
    protected static $_customerData = array();
    protected static $_addressData = array();

    /**
     * Precondition:
     * Create new customer
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        self::$_customerData = $this->loadDataSet('Customers', 'generic_customer_account');
        self::$_addressData = $this->loadDataSet('Customers', 'generic_address');
        $this->customerHelper()->createCustomer(self::$_customerData, self::$_addressData);
        $this->assertMessagePresent('success', 'success_saved_customer');
    }

    /**
     * Preconditions:
     * Log in to Backend.
     * Navigate to System -> Export/p>
     */
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        //Step 1
        $this->navigate('import');
    }

    /**
     * Has been excluded from functionality scope
     * Validation Result block
     * Verify that Validation Result block will be displayed after checking data of import file
     * Precondition: at least one customer exists, one file is generated after export
     * Steps:
     * 1. Go to System -> Import/ Export -> Import
     * 2. In the drop-down "Entity Type" select "Customers"
     * 3. In "Import Format Version" dropdown field choose "Magento 1.7 format" parameter
     * 4. In "Import Behavior" dropdown field choose "Append Complex Data" parameter
     * 5. Select file to import
     * 6. Click "Check Data" button.
     * Expected: validation and success messages are correct
     *
     * @test
     * @TestlinkId TL-MAGE-1108
     * @group skip_due_to_bug
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
        $this->importExportHelper()->chooseImportOptions('Customers', 'Append Complex Data', 'Magento 1.7 format');
        //Step 5-6
        $importData = $this->importExportHelper()->import($report);
        //Verifying
        $this->assertEquals(
            'Checked rows: ' . count($report) . ', checked entities: '
                . $numberOfEntities . ', invalid rows: 0, total errors: 0',
            $importData['validation']['validation'][0],
            'Validation message is not correct'
        );
        $this->assertEquals(
            'File is valid! To start import process press "Import" button  Import',
            $importData['validation']['success'][0], 'Success message is not correct'
        );
    }

    /**
     * Required columns
     * Steps
     * 1. Go to System -> Import / Export -> Import
     * 2. Select Entity Type: Customers
     * 3. Select Export Format Version: Magento 1.7 format
     * 4. In Import Behavior dropdown field choose Append Complex Data parameter
     * 5. Choose file from precondition
     * 6. Click on Check Data
     * 7. Click on Import button
     * 8. Open Customers -> Manage Customers
     * 9. Open each of imported customers
     * Expected: 
     * After step 6
     * Verify that file is valid, the message 'File is valid!' is displayed
     * After step 7
     * Verify that import starting. The message 'Import successfully done.' is displayed
     * After step 8
     * Verify that imported customers display on customers grid
     * After step 9
     * Verify that all Customer information was imported
     *
     * @test
     * @dataProvider importWithRequiredColumnsData
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
        $this->assertArrayHasKey(
            'import',
            $report,
            'Import has been finished with issues:' . print_r($report, true) . print_r($data, true)
        );
        $this->assertArrayHasKey(
            'success',
            $report['import'],
            'Import has been finished with issues:' . print_r($report, true) . print_r($data, true)
        );
        //Check updated customer
        self::$_customerData['first_name'] = $data[0]['firstname'];
        self::$_customerData['last_name'] = $data[0]['lastname'];
        $this->navigate('manage_customers');
        $this->addParameter(
            'customer_first_last_name',
            self::$_customerData['first_name'] . ' ' . self::$_customerData['last_name']
        );
        $this->customerHelper()->openCustomer(array('email' => self::$_customerData['email']));
        $this->assertTrue(
            $this->verifyForm(self::$_customerData, 'account_information'),
            'Customer has not been updated'
        );
        //Check new customer
        $customerData = $this->loadDataSet(
            'Customers', 'generic_customer_account', array(
                'email' => $data[1]['email'],
                'first_name' => $data[1]['firstname'],
                'last_name' => $data[1]['lastname'],
            )
        );
        $this->navigate('manage_customers');
        $this->assertTrue(
            $this->customerHelper()->isCustomerPresentInGrid($customerData),
            'Customer has not been created'
        );
        $this->addParameter(
            'customer_first_last_name',
            $customerData['first_name'] . ' ' . $customerData['last_name']
        );
        $this->customerHelper()->openCustomer(
            array('email' => strtolower($customerData['email']))
        );
        $this->verifyForm($customerData, 'account_information');
    }

    public function importWithRequiredColumnsData()
    {
        return array(
            array(
                array($this->loadDataSet('ImportExport', 'generic_customer_csv', array(
                    'firstname' => 'New First Name',
                    'lastname' => 'New Last Name',
                    '_store' => '',
                    )
                ),
                $this->loadDataSet('ImportExport', 'generic_customer_csv', array('_store' => ''))
                )
            )
        );
    }
}