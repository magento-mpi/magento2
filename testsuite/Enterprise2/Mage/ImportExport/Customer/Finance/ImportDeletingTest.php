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
class Enterprise2_Mage_ImportExport_Deleting_FinanceTest extends Mage_Selenium_TestCase
{
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
     * <p>Deleting Customer Finance</p>
     * <p>Preconditions:</p>
     * <p>1. Create two customers in Customers-> Manage Customers</p>
     * <p>2. Create csv file with both customers finances: first with all attributes, second only with unique key</p>
     * <p>Steps</p>
     * <p>1. In System -> Import/ Export -> Import in drop-down "Entity Type" select "Customer Finances"</p>
     * <p>2. Select "Delete" in selector "Import Behavior"</p>
     * <p>3. Choose file from precondition</p>
     * <p>4. Press "Check Data"</p>
     * <p>5. Press "Import" button</p>
     * <p>6. Open Customers-> Manage Customers</p>
     * <p>Expected: Verify that finances are deleted for both customers</p>
     *
     * @test
     * @dataProvider importData
     * @TestlinkId TL-MAGE-5681
     */
    public function deletingCustomerFinance($data)
    {
        //Create Customer1
        $this->navigate('manage_customers');
        $userData1 = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData1);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Create Customer2
        $this->navigate('manage_customers');
        $userData2 = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData2);
        $this->assertMessagePresent('success', 'success_saved_customer');

        //Update Customer 1
        $this->addParameter('customer_first_last_name', $userData1['first_name'] . ' ' . $userData1['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userData1['email']));

        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '25'));
        $userData1['update_balance'] = '25';
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => $userData1['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '50'));
        $userData1['update_balance'] = '50';
        $this->assertMessagePresent('success', 'success_saved_customer');

        //Update Customer 2
        $this->addParameter('customer_first_last_name', $userData2['first_name'] . ' ' . $userData2['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userData2['email']));

        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '30'));
        $userData2['update_balance'] = '30';
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => $userData2['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '10'));
        $userData2['update_balance'] = '10';
        $this->assertMessagePresent('success', 'success_saved_customer');

        $data[0]['_email'] = $userData1['email'];

        $data[1]['_email'] = $userData2['email'];

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
        $this->addParameter('customer_first_last_name', $userData1['first_name'] . ' ' . $userData1['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userData1['email']));
        //Verify customer account
        $this->assertEquals('$0.00', $this->customerHelper()->getStoreCreditBalance(),
            'Store Credit balance is not deleted');
        $this->assertEquals('0', $this->customerHelper()->getRewardPointsBalance(),
            'Reward Points Balance is not deleted');
        //Step 7. Second Customer
        $this->navigate('manage_customers');
        $this->addParameter('customer_first_last_name', $userData2['first_name'] . ' ' . $userData2['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userData2['email']));
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
                        array(
                            'store_credit' => '25',
                            'reward_points' => '50'
                        )
                    ),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                        array(
                            'store_credit' => '80',
                            'reward_points' => '100'
                        )
                    )
                )
            )
        );
    }
    /**
     * <p>Deleting Customer Finance with different email or website</p>
     * <p>Preconditions:</p>
     * <p>1. Create two customers in Customers-> Manage Customers</p>
     * <p>2. Update for both customers "Store Credit" and "Reward Points"</p>
     * <p>3. Create .csv file with incorrect email for first customer, with incorrect website for second customer</p>
     * <p>Steps</p>
     * <p>1. In System -> Import/ Export -> Import in drop-down "Entity Type" select "Customer Finances"</p>
     * <p>2. Select "Delete" in selector "Import Behavior"</p>
     * <p>3. Choose file from precondition</p>
     * <p>4. Press "Check Data"</p>
     * <p>5. Press "Import" button</p>
     * <p>6. Open Customers-> Manage Customers</p>
     * <p>Expected: Verify that finances for both customers aren't deleted</p>
     *
     * @test
     * @dataProvider importFinanceData
     * @TestlinkId TL-MAGE-5682
     */
    public function deletingCustomerFinanceWithDifferentEmailOrWebsite($data)
    {
        //Create Customer1
        $this->navigate('manage_customers');
        $userData1 = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData1);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Create Customer2
        $this->navigate('manage_customers');
        $userData2 = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData2);
        $this->assertMessagePresent('success', 'success_saved_customer');

        //Update Customer 1
        $this->addParameter('customer_first_last_name', $userData1['first_name'] . ' ' . $userData1['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userData1['email']));

        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '25'));
        $userData1['update_balance'] = '25';
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => $userData1['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '50'));
        $userData1['update_balance'] = '50';
        $this->assertMessagePresent('success', 'success_saved_customer');

        //Update Customer 2
        $this->addParameter('customer_first_last_name', $userData2['first_name'] . ' ' . $userData2['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userData2['email']));

        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '75'));
        $userData2['update_balance'] = '75';
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => $userData2['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '100'));
        $userData2['update_balance'] = '100';
        $this->assertMessagePresent('success', 'success_saved_customer');

        $data[1]['_email'] = $userData2['email'];

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
        $this->addParameter('customer_first_last_name', $userData1['first_name'] . ' ' . $userData1['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userData1['email']));
        //Verify customer account
        $this->assertEquals('$25.00', $this->customerHelper()->getStoreCreditBalance(),
            'Store Credit balance is deleted');
        $this->assertEquals('50', $this->customerHelper()->getRewardPointsBalance(),
            'Reward Points Balance is deleted');
        //Step 7. Second Customer
        $this->navigate('manage_customers');
        $this->addParameter('customer_first_last_name', $userData2['first_name'] . ' ' . $userData2['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userData2['email']));
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
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                        array(
                            '_email' => 'not_existing_email_qqqqq@example.co',
                            'store_credit' => '25',
                            'reward_points' => '50'
                        )
                    ),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                        array(
                            '_website' => $this->generate('string', 30, ':digit:'),
                            'store_credit' => '75',
                            'reward_points' => '100'
                            )
                        )
                )
            )
        );
    }
    /**
     * <p>Deleting customer finances with wrong or not cpecified _finance_website</p>
     * <p>Preconditions:</p>
     * <p>1. Create two customers in Customers-> Manage Customers</p>
     * <p>2. Update for both customers "Store Credit" and "Reward Points"</p>
     * <p>3. Create csv file with empty _finance_website for customer1, with incorrect  _finance_website for customer2</p>
     * <p>Steps</p>
     * <p>1. In System -> Import/ Export -> Import in drop-down "Entity Type" select "Customer Finances"</p>
     * <p>2. Select "Delete" in selector "Import Behavior"</p>
     * <p>3. Choose file from precondition</p>
     * <p>4. Press "Check Data"</p>
     * <p>5. Open Customers-> Manage Customers</p>
     * <p>Expected: After step 6 the message 'File is totaly invalid' is appeared</p>
     * <p>Expected: After step 7 the finances for both customers aren't deleted</p>
     *
     * @test
     * @dataProvider importFinanceData1
     * @TestlinkId TL-MAGE-5717
     */
     public function deletingCustomerFinanceWithWrongFinanceWebsite($data)
     {

         //Create Customer1
         $this->navigate('manage_customers');
         $userData1 = $this->loadDataSet('Customers', 'generic_customer_account');
         $this->customerHelper()->createCustomer($userData1);
         $this->assertMessagePresent('success', 'success_saved_customer');
         //Create Customer2
         $this->navigate('manage_customers');
         $userData2 = $this->loadDataSet('Customers', 'generic_customer_account');
         $this->customerHelper()->createCustomer($userData2);
         $this->assertMessagePresent('success', 'success_saved_customer');

         //Update Customer 1
         $this->addParameter('customer_first_last_name', $userData1['first_name'] . ' ' . $userData1['last_name']);
         $this->customerHelper()->openCustomer(array('email' => $userData1['email']));

         $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '100'));
         $userData1['update_balance'] = '100';
         $this->assertMessagePresent('success', 'success_saved_customer');
         $this->customerHelper()->openCustomer(array('email' => $userData1['email']));
         $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '150'));
         $userData1['update_balance'] = '150';
         $this->assertMessagePresent('success', 'success_saved_customer');

         //Update Customer 2
         $this->addParameter('customer_first_last_name', $userData2['first_name'] . ' ' . $userData2['last_name']);
         $this->customerHelper()->openCustomer(array('email' => $userData2['email']));

         $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '200'));
         $userData2['update_balance'] = '200';
         $this->assertMessagePresent('success', 'success_saved_customer');
         $this->customerHelper()->openCustomer(array('email' => $userData2['email']));
         $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '250'));
         $userData2['update_balance'] = '250';
         $this->assertMessagePresent('success', 'success_saved_customer');

         $data[0]['_email'] = $userData1['email'];;
         $data[1]['_email'] = $userData2['email'];

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
         $this->addParameter('customer_first_last_name', $userData1['first_name'] . ' ' . $userData1['last_name']);
         $this->customerHelper()->openCustomer(array('email' => $userData1['email']));
         //Verify customer account
         $this->assertEquals('$100.00', $this->customerHelper()->getStoreCreditBalance(),
             'Store Credit balance is deleted');
         $this->assertEquals('150', $this->customerHelper()->getRewardPointsBalance(),
             'Reward Points Balance is deleted');
         //Step 6. Second Customer
         $this->navigate('manage_customers');
         $this->addParameter('customer_first_last_name', $userData2['first_name'] . ' ' . $userData2['last_name']);
         $this->customerHelper()->openCustomer(array('email' => $userData2['email']));
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
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                                    array(
                                        'store_credit' => '100',
                                        'reward_points' => '150',
                                        '_finance_website' => ''
                                    )
                    ),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                                    array(
                                        'store_credit' => '200',
                                        'reward_points' => '250',
                                        '_finance_website' => $this->generate('string', 30, ':digit:')
                                    )
                    )
                )
            )
        );
    }
}