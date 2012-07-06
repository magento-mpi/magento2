<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iglazunova
 * Date: 6/20/12
 * Time: 1:03 PM
 * To change this template use File | Settings | File Templates.
 */
class Enterprise2_Mage_ImportExport_EmptyValues_FinanceTest extends Mage_Selenium_TestCase
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
        //Step 1
        $this->navigate('import');
    }
    /**
     * <p>Empty values for existing attributes in csv for Customer Finances</p>
     * <p>Preconditions:</p>
     * <p>1. Create two customers</p>
     * <p>2. Add some values to Reward Points and Store Credit for both customers</p>
     * <p>3. CSV file prepared that contains:<br>
     * empty columns Reward Points and Store Credit for first customer<br>
     * value "0" in columns Reward Points and Store Credit for second customer</p>
     * <p>Steps</p>
     * <p>1. In System -> Import/ Export -> Import in drop-down "Entity Type" select "Customers"</p>
     * <p>2. Select "Add/Update Complex Data" in selector "Import Behavior"</p>
     * <p>3. Select "Magento 2.0 format"</p>
     * <p>4. Select "Customer Finances"</p>
     * <p>5. Choose file from precondition</p>
     * <p>6. Press "Check Data"</p>
     * <p>7. Press "Import" button</p>
     * <p>8. Open Customers-> Manage Customers</p>
     * <p>9. Open customers from precondition</p>
     * <p>Expected: Customer1 has values as in precondition. Customer2 has "0" for Reward Points and Store Credit</p>
     *
     * @test
     * @dataProvider importData
     * @TestlinkId TL-MAGE-5644
     */
    public function emptyValuesAttributesInCsv($data)
    {
        //Create Customer1
        $this->navigate('manage_customers');
        $userData1 = $this->loadDataSet('ImportExport', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData1);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Create Customer2
        $this->navigate('manage_customers');
        $userData2 = $this->loadDataSet('ImportExport', 'generic_customer_account');
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

        $data[0]['email'] = $userData1['email'];
        $data[0]['store_credit'] = '';
        $data[0]['reward_points'] = '';

        $data[1]['email'] = $userData2['email'];
        $data[1]['store_credit'] = '0';
        $data[1]['reward_points'] = '0';

        //Step1
        $this->admin('import');
        $this->importExportHelper()->chooseImportOptions('Customers', 'Add/Update Complex Data',
            'Magento 2.0 format', 'Customer Finances');
        //Step 5, 6, 7
        $report = $this->importExportHelper()->import($data);
        //Check import
        $this->assertArrayHasKey('import', $report, 'Import has been finished with issues: '
            . print_r($report));
        $this->assertArrayHasKey('success', $report['import'], 'Import has been finished with issues: '
            . print_r($report));
        //Step 8
        $this->navigate('manage_customers');
        //Step 9. First Customer
        $this->addParameter('customer_first_last_name', $userData1['first_name'] . ' ' . $userData1['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userData1['email']));
        //Verify customer account
        $this->assertEquals('$100.00', $this->customerHelper()->getStoreCreditBalance(),
            'Adding customer credit score balance is failed');
        $this->assertEquals('150', $this->customerHelper()->getRewardPointsBalance(),
            'Adding customer reward points balance is failed');
        //Step 9. Second Customer
        $this->navigate('manage_customers');
        $this->addParameter('customer_first_last_name', $userData2['first_name'] . ' ' . $userData2['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userData2['email']));
        //Verify customer account
        $this->openTab('store_credit');
        $this->assertEquals('$0.00', $this->customerHelper()->getStoreCreditBalance(),
            'Adding customer credit score balance is failed');
        $this->assertEquals('0', $this->customerHelper()->getRewardPointsBalance(),
            'Adding customer reward points balance is failed');
    }
    public function importData()
    {
        return array(
            array(
                array(
                    $this->loadDataSet('ImportExport', 'generic_finance_csv'),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv')
                )
            )
        );
    }
}