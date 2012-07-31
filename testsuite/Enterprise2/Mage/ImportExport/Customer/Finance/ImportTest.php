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
 * Customer Finances Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_ImportExport_ImportFinanceTest extends Mage_Selenium_TestCase
{
    protected static $customerData = array();

    /**
     * <p>Precondition:</p>
     * <p>Create 2 customers</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        self::$customerData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer(self::$customerData);
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
    }

    /**
     * <p>Required columns</p>
     * <p>Steps</p>
     * <p>1. Go to System -> Import / Export -> Import</p>
     * <p>2. Select Entity Type: Customer Finances</p>
     * <p>3. Select Import Behavior: Add/Update Complex Data</p>
     * <p>4. Choose file from precondition</p>
     * <p>5. Click on Check Data</p>
     * <p>6. Click on Import button</p>
     * <p>7. Open Customers -> Manage Customers</p>
     * <p>8. Open each of imported customers</p>
     * <p>After step 5</p>
     * <p>Verify that file is valid, the message 'File is valid!' is displayed</p>
     * <p>After step 6</p>
     * <p>Verify that import starting. The message 'Import successfully done.' is displayed</p>
     * <p>After step 7</p>
     * <p>Verify that all Customers finance information was imported</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5624
     */
    public function importWithRequiredColumns()
    {
        //Precondition: create 2 new customers
        // 0.1. create two customers
        $this->navigate('manage_customers');
        $userData1 = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData1);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('manage_customers');
        $userData2 = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData2);
        $this->addParameter('customer_first_last_name', $userData2['first_name'] . ' ' . $userData2['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userData2['email']));
        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '1234'));
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => $userData2['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '4321'));
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('import');
        //Step 1
        $this->importExportHelper()->chooseImportOptions('Customer Finances', 'Add/Update Complex Data');
        //Generated CSV data
        $customerDataRow1 = $this->loadDataSet('ImportExport', 'generic_finance_csv',
            array(
                '_email' => $userData1['email'],
                'store_credit' => '4321.0000',
                'reward_points' => '1234'
            ));
        $customerDataRow2 = $this->loadDataSet('ImportExport', 'generic_finance_csv',
            array(
                '_email' => $userData2['email'],
                '_finance_website' => 'base',
                'store_credit' => '4321.0000',
                'reward_points' => '1234'
            ));
        //Build CSV array
        $data = array(
            $customerDataRow1,
            $customerDataRow2
        );
        //Import file with default flow
        $report = $this->importExportHelper()->import($data) ;
        //Check import
        $this->assertArrayHasKey('import', $report, 'Import has been finished with issues:' .
            print_r($report, true) . print_r($data, true));
        $this->assertArrayHasKey('success', $report['import'], 'Import has been finished with issues:' .
            print_r($report, true) . print_r($data, true));
        //Check customers
        $this->navigate('manage_customers');
        //Check updated customer
        $this->addParameter('customer_first_last_name',
            $userData1['first_name'] . ' ' . $userData1['last_name']);
        $this->customerHelper()->openCustomer(
            array(
                'email' => $userData1['email']
            ));
        $this->assertEquals('$4,321.00', $this->customerHelper()->getStoreCreditBalance(),
            'Adding customer credit score balance is failed');
        $this->assertEquals('1234', $this->customerHelper()->getRewardPointsBalance(),
            'Adding customer reward points balance is failed');
        $this->navigate('manage_customers');
        $this->addParameter('customer_first_last_name',
            $userData2['first_name'] . ' ' . $userData2['last_name']);
        $this->customerHelper()->openCustomer(
            array(
                'email' => $userData2['email']
            ));
        $this->assertEquals('$4,321.00', $this->customerHelper()->getStoreCreditBalance(),
            'Updating customer credit score balance is failed');
        $this->assertEquals('1234', $this->customerHelper()->getRewardPointsBalance(),
            'Updating customer reward points balance is failed');
    }

    /**
     * <p>Partial Import</p>
     * <p>Verify that if import file has some invalid data, then import will be finished partially</p>
     * <p>Precondition: one customer exists in the system. Csv file contains two rows: valid customer finances data,
     * invalid customer finances data (store credit in text format)</p>
     * <p>Steps</p>
     * <p>1. Go to System -> Import/ Export -> Import</p>
     * <p>2. Select Entity Type: Customer Finances</p>
     * <p>3. Select Import Behavior: Add/Update Complex Data</p>
     * <p>4. Choose first file from precondition, click "Check Data" button, Press "Import" button</p>
     * <p>Expected: messages "Invalid value for 'store_credit' in rows: 2", "Invalid value for 'reward_points' in
     * rows: 2", "Please fix errors and re-upload file or simply press "Import" button to skip rows with
     * errors" and "Checked rows: 2, checked entities: 2, invalid rows: 1, total errors: 2" are displayed</p>
     * <p>5. Open customers</p>
     * <p>Expected: valid finance data information was imported correctly</p>
     *
     * @test
     * @dataProvider partialImportData
     * @TestlinkId TL-MAGE-5633
     */
    public function partialImport($csvData, $validationMessage)
    {
        //Set correct email for csv data
        foreach ($csvData as $key => $value) {
            if (array_key_exists('_email', $csvData[$key]) && $csvData[$key]['_email'] == '<realEmail>'){
                $csvData[$key]['_email'] = self::$customerData['email'];
            }
        }
        //Step 1
        $this->navigate('import');
        //Steps 2-3
        $this->importExportHelper()->chooseImportOptions('Customer Finances', 'Add/Update Complex Data');
        //Step 4
        $importData = $this->importExportHelper()->import($csvData);
        //Verifying import
        $this->assertEquals($validationMessage, $importData, 'Import has been finished with issues');
        //Step 5
        $this->navigate('manage_customers');
        $this->addParameter('customer_first_last_name', self::$customerData['first_name'] . ' '
            . self::$customerData['last_name']);
        $this->customerHelper()->openCustomer(array('email' => self::$customerData['email']));
        //Verifying finance data
        $this->assertEquals('$' . $csvData[0]['store_credit'] . '.00', $this->customerHelper()->getStoreCreditBalance(),
            'Store credit has not been added');
        $this->assertEquals($csvData[0]['reward_points'], $this->customerHelper()->getRewardPointsBalance(),
            'Reward points have not been added');
    }

    public function partialImportData()
    {
        $csvRow1 = $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                '_email' => '<realEmail>',
                'store_credit' => '100',
                'reward_points' => '200',
            )
        );
        $csvRow2 = $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                '_email' => '<realEmail>',
                'store_credit' => 'store_credit',
                'reward_points' => 'reward_points',
            )
        );

        $csvRows = array($csvRow1, $csvRow2);

        $validationMessage = array('validation' => array(
            'error' => array(
                "Invalid value for 'store_credit' in rows: 2",
                "Invalid value for 'reward_points' in rows: 2"
            ),
            'validation' => array(
            "Please fix errors and re-upload file or simply press \"Import\" button to skip rows with errors  Import",
            "Checked rows: 2, checked entities: 2, invalid rows: 1, total errors: 2",
            )
            ),
            'import' => array(
                'success' => array("Import successfully done."),
            ),
        );

        return array(array($csvRows, $validationMessage));
    }
}