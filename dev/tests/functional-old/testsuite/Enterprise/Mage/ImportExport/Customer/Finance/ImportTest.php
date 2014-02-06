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
class Enterprise_Mage_ImportExport_Customer_Finance_ImportTest extends Mage_Selenium_TestCase
{
    protected static $_customerData = array();

    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        self::$_customerData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer(self::$_customerData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/disable_secret_key');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
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
        // 0.1. create two customers
        $this->navigate('manage_customers');
        $userDataOne = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userDataOne);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('manage_customers');
        $userDataTwo = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userDataTwo);
        $this->customerHelper()->openCustomer(array('email' => $userDataTwo['email']));
        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '1234'));
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => $userDataTwo['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '4321'));
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('import');
        //Step 1
        $this->importExportHelper()->chooseImportOptions('Customer Finances', 'Add/Update Complex Data');
        //Generated CSV data
        $userDataRowOne = $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
            '_email' => $userDataOne['email'],
            'store_credit' => '4321.0000',
            'reward_points' => '1234'
        ));
        $userDataRowTwo = $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
            '_email' => $userDataTwo['email'],
            '_finance_website' => 'base',
            'store_credit' => '4321.0000',
            'reward_points' => '1234'
        ));
        //Build CSV array
        $data = array($userDataRowOne, $userDataRowTwo);
        //Import file with default flow
        $report = $this->importExportHelper()->import($data);
        //Check import
        $this->assertArrayHasKey('import', $report, 'Import has been finished with issues:' .
            print_r($report, true) . print_r($data, true));
        $this->assertArrayHasKey('success', $report['import'], 'Import has been finished with issues:' .
            print_r($report, true) . print_r($data, true));
        //Check customers
        $this->navigate('manage_customers');
        //Check updated customer
        $this->customerHelper()->openCustomer(array('email' => $userDataOne['email']));
        $this->assertEquals('$4,321.00', $this->customerHelper()->getStoreCreditBalance(),
            'Adding customer credit score balance is failed');
        $this->assertEquals('1234', $this->customerHelper()->getRewardPointsBalance(),
            'Adding customer reward points balance is failed');
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => $userDataTwo['email']));
        $this->assertEquals('$4,321.00', $this->customerHelper()->getStoreCreditBalance(),
            'Updating customer credit score balance is failed');
        $this->assertEquals('1234', $this->customerHelper()->getRewardPointsBalance(),
            'Updating customer reward points balance is failed');
    }

    /**
     * Partial Import
     * Verify that if import file has some invalid data, then import will be finished partially
     * Precondition: one customer exists in the system. Csv file contains two rows: valid customer finances data,
     * invalid customer finances data (store credit in text format)
     *
     * @test
     * @dataProvider partialImportData
     * @TestlinkId TL-MAGE-5633
     */
    public function partialImport($csvData, $validationMessage)
    {
        //Precondition
        $this->navigate('manage_customers');
        $newCustomerData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($newCustomerData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $customerData = array(self::$_customerData, $newCustomerData);
        //Set correct email for csv data
        foreach ($csvData as $key => $value) {
            if (array_key_exists('_email', $value) && $value['_email'] == '<realEmail>') {
                $csvData[$key]['_email'] = $customerData[$key]['email'];
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
        $this->customerHelper()->openCustomer(array('email' => self::$_customerData['email']));
        //Verifying finance data
        $this->assertEquals('$' . $csvData[0]['store_credit'] . '.00', $this->customerHelper()->getStoreCreditBalance(),
            'Store credit has not been added');
        $this->assertEquals($csvData[0]['reward_points'], $this->customerHelper()->getRewardPointsBalance(),
            'Reward points have not been added');
    }

    public function partialImportData()
    {
        $csvRows[0] = $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
            '_email' => '<realEmail>',
            'store_credit' => '100',
            'reward_points' => '200',
        ));
        $csvRows[1] = $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
            '_email' => '<realEmail>',
            'store_credit' => 'store_credit',
            'reward_points' => 'reward_points',
        ));

        $validationMessage = array(
            'validation' => array(
                'error' => array(
                    "Please correct the value for 'store_credit'. in rows: 2",
                    "Please correct the value for 'reward_points'. in rows: 2"
                ),
                'validation' => array(
                    "Please fix errors and re-upload file or simply press \"Import\" button to skip rows with errors  Import",
                    "Checked rows: 2, checked entities: 2, invalid rows: 1, total errors: 2",
                )
            ),
            'import' => array(
                'success' => array('Import successfully done'),
            ),
        );

        return array(array($csvRows, $validationMessage));
    }
}
