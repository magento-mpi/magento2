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
class Enterprise_Mage_ImportExport_Customer_Finance_ImportDeletingTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
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
     * Deleting Customer Finance
     *
     * @test
     * @dataProvider importData
     * @TestlinkId TL-MAGE-5681
     */
    public function deletingCustomerFinance($data)
    {
        //Create Customer1
        $this->navigate('manage_customers');
        $userDataDelete[0] = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userDataDelete[0]);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Create Customer2
        $this->navigate('manage_customers');
        $userDataDelete[1] = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userDataDelete[1]);
        $this->assertMessagePresent('success', 'success_saved_customer');

        //Update Customer 1
        $this->customerHelper()->openCustomer(array('email' => $userDataDelete[0]['email']));
        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '25'));
        $userDataDelete[0]['update_balance'] = '25';
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => $userDataDelete[0]['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '50'));
        $userDataDelete[0]['update_balance'] = '50';
        $this->assertMessagePresent('success', 'success_saved_customer');

        //Update Customer 2
        $this->customerHelper()->openCustomer(array('email' => $userDataDelete[1]['email']));
        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '30'));
        $userDataDelete[1]['update_balance'] = '30';
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => $userDataDelete[1]['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '10'));
        $userDataDelete[1]['update_balance'] = '10';
        $this->assertMessagePresent('success', 'success_saved_customer');

        $data[0]['_email'] = $userDataDelete[0]['email'];
        $data[1]['_email'] = $userDataDelete[1]['email'];

        //Steps 1-2
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Customer Finances', 'Delete Entities');
        //Steps 3-5
        $report = $this->importExportHelper()->import($data);
        //Check import
        $this->assertArrayHasKey('import', $report, 'Import has been finished with issues:');
        $this->assertArrayHasKey('success', $report['import'], 'Import has been finished with issues:');
        //Step 6
        $this->navigate('manage_customers');
        //Step 7. First Customer
        $this->customerHelper()->openCustomer(array('email' => $userDataDelete[0]['email']));
        //Verify customer account
        $this->assertEquals('0', $this->customerHelper()->getRewardPointsBalance(),
            'Reward Points Balance is not deleted');
        $this->assertEquals('$0.00', $this->customerHelper()->getStoreCreditBalance(),
            'Store Credit balance is not deleted');
        //Step 7. Second Customer
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => $userDataDelete[1]['email']));
        //Verify customer account
        $this->assertEquals('$0.00', $this->customerHelper()->getStoreCreditBalance(),
            'Store Credit balance is not deleted');
        $this->assertEquals('0', $this->customerHelper()->getRewardPointsBalance(),
            'Reward Points Balance is not deleted');
    }

    public function importData()
    {
        return array(
            array(
                array(
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                        array('store_credit' => '25', 'reward_points' => '50')),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                        array('store_credit' => '80', 'reward_points' => '100'))
                )
            )
        );
    }

    /**
     * Deleting Customer Finance with different email or website
     *
     * @test
     * @dataProvider importFinanceData
     * @TestlinkId TL-MAGE-5682
     */
    public function deletingCustomerFinanceWithDifferentEmailOrWebsite($data)
    {
        //Create Customer1
        $this->navigate('manage_customers');
        $userDataOne = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userDataOne);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Create Customer2
        $this->navigate('manage_customers');
        $userDataTwo = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userDataTwo);
        $this->assertMessagePresent('success', 'success_saved_customer');

        //Update Customer 1
        $this->customerHelper()->openCustomer(array('email' => $userDataOne['email']));

        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '25'));
        $userDataOne['update_balance'] = '25';
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => $userDataOne['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '50'));
        $userDataOne['update_balance'] = '50';
        $this->assertMessagePresent('success', 'success_saved_customer');

        //Update Customer 2
        $this->customerHelper()->openCustomer(array('email' => $userDataTwo['email']));

        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '75'));
        $userDataTwo['update_balance'] = '75';
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => $userDataTwo['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '100'));
        $userDataTwo['update_balance'] = '100';
        $this->assertMessagePresent('success', 'success_saved_customer');

        $data[1]['_email'] = $userDataTwo['email'];

        //Step 1-2
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Customer Finances', 'Delete Entities');
        //Steps 3-5
        $report = $this->importExportHelper()->import($data);
        //Check import
        $this->assertArrayNotHasKey('import', $report, 'Import has been finished with issues:');
        $this->assertArrayHasKey('error', $report['validation'], 'Import has been finished with issues:');
        //Step 6
        $this->navigate('manage_customers');
        //Step 7. First Customer
        $this->customerHelper()->openCustomer(array('email' => $userDataOne['email']));
        //Verify customer account
        $this->assertEquals('$25.00', $this->customerHelper()->getStoreCreditBalance(),
            'Store Credit balance is deleted');
        $this->assertEquals('50', $this->customerHelper()->getRewardPointsBalance(),
            'Reward Points Balance is deleted');
        //Step 7. Second Customer
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => $userDataTwo['email']));
        //Verify customer account
        $this->assertEquals('$75.00', $this->customerHelper()->getStoreCreditBalance(),
            'Store Credit balance is deleted');
        $this->assertEquals('100', $this->customerHelper()->getRewardPointsBalance(),
            'Reward Points Balance is deleted');
    }

    public function importFinanceData()
    {
        return array(
            array(
                array(
                    $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                        '_email' => 'not_existing_email_qqqqq@example.co',
                        'store_credit' => '25',
                        'reward_points' => '50'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                        '_website' => $this->generate('string', 30, ':digit:'),
                        'store_credit' => '75',
                        'reward_points' => '100'
                    ))
                )
            )
        );
    }

    /**
     * Deleting customer finances with wrong or not cpecified _finance_website
     *
     * @test
     * @dataProvider importFinanceData1
     * @TestlinkId TL-MAGE-5717
     */
    public function deletingCustomerFinanceWithWrongFinanceWebsite($data)
    {
        //Create Customer1
        $this->navigate('manage_customers');
        $userDataOne = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userDataOne);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Create Customer2
        $this->navigate('manage_customers');
        $userDataTwo = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userDataTwo);
        $this->assertMessagePresent('success', 'success_saved_customer');

        //Update Customer 1
        $this->customerHelper()->openCustomer(array('email' => $userDataOne['email']));

        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '100'));
        $userDataOne['update_balance'] = '100';
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => $userDataOne['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '150'));
        $userDataOne['update_balance'] = '150';
        $this->assertMessagePresent('success', 'success_saved_customer');

        //Update Customer 2
        $this->customerHelper()->openCustomer(array('email' => $userDataTwo['email']));

        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '200'));
        $userDataTwo['update_balance'] = '200';
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => $userDataTwo['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '250'));
        $userDataTwo['update_balance'] = '250';
        $this->assertMessagePresent('success', 'success_saved_customer');

        $data[0]['_email'] = $userDataOne['email'];;
        $data[1]['_email'] = $userDataTwo['email'];

        //Steps 1-2
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Customer Finances', 'Delete Entities');
        //Steps 3-4
        $report = $this->importExportHelper()->import($data);
        //Check import
        $this->assertArrayNotHasKey('import', $report,
            'Import has been finished with issues:');
        $this->assertArrayHasKey('error', $report['validation'],
            'Import has been finished with issues:');
        $this->assertEquals('Finance information website is not specified in rows: 1',
            $report['validation']['error'][0]);
        $this->assertEquals('Invalid value in Finance information website column in rows: 2',
            $report['validation']['error'][1]);
        //Step 5
        $this->navigate('manage_customers');
        //Step 6. First Customer
        $this->customerHelper()->openCustomer(array('email' => $userDataOne['email']));
        //Verify customer account
        $this->assertEquals('$100.00', $this->customerHelper()->getStoreCreditBalance(),
            'Store Credit balance is deleted');
        $this->assertEquals('150', $this->customerHelper()->getRewardPointsBalance(),
            'Reward Points Balance is deleted');
        //Step 6. Second Customer
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => $userDataTwo['email']));
        //Verify customer account
        $this->assertEquals('$200.00', $this->customerHelper()->getStoreCreditBalance(),
            'Store Credit balance is deleted');
        $this->assertEquals('250', $this->customerHelper()->getRewardPointsBalance(),
            'Reward Points Balance is deleted');
    }

    public function importFinanceData1()
    {
        return array(
            array(
                array(
                    $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                        'store_credit' => '100',
                        'reward_points' => '150',
                        '_finance_website' => ''
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                        'store_credit' => '200',
                        'reward_points' => '250',
                        '_finance_website' => $this->generate('string', 30, ':digit:')
                    ))
                )
            )
        );
    }
}