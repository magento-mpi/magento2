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
 * Customer Finances Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_ImportExport_EmptyValues_FinanceTest extends Mage_Selenium_TestCase
{
    /**
     * Preconditions:
     * Log in to Backend.
     * Navigate to System -> Export
     */
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        //Step 1
        $this->navigate('import');
    }
    /**
     * Empty values for existing attributes in csv for Customer Finances
     * Preconditions:
     * 1. Create two customers
     * 2. Add some values to Reward Points and Store Credit for both customers
     * 3. CSV file prepared that contains:<br>
     * empty columns Reward Points and Store Credit for first customer<br>
     * value "0" in columns Reward Points and Store Credit for second customer
     * Steps
     * 1. In System -> Import/ Export -> Import in drop-down "Entity Type" select "Customer Finances"
     * 2. Select "Add/Update Complex Data" in selector "Import Behavior"
     * 3. Choose file from precondition
     * 4. Press "Check Data"
     * 5. Press "Import" button
     * 6. Open Customers-> Manage Customers
     * 7. Open customers from precondition
     * Expected: Customer1 has values as in precondition. Customer2 has "0" for Reward Points and Store Credit
     *
     * @test
     * @dataProvider importData
     * @TestlinkId TL-MAGE-5644
     */
    public function emptyValuesAttributesInCsv($data)
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
        $this->addParameter('customer_first_last_name', $userDataOne['first_name'] . ' ' . $userDataOne['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userDataOne['email']));

        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '100'));
        $userDataOne['update_balance'] = '100';
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => $userDataOne['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '150'));
        $userDataOne['update_balance'] = '150';
        $this->assertMessagePresent('success', 'success_saved_customer');

        //Update Customer 2
        $this->addParameter('customer_first_last_name', $userDataTwo['first_name'] . ' ' . $userDataTwo['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userDataTwo['email']));

        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '200'));
        $userDataTwo['update_balance'] = '200';
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => $userDataTwo['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '250'));
        $userDataTwo['update_balance'] = '250';
        $this->assertMessagePresent('success', 'success_saved_customer');

        $data[0]['_email'] = $userDataOne['email'];
        $data[0]['store_credit'] = '';
        $data[0]['reward_points'] = '';

        $data[1]['_email'] = $userDataTwo['email'];
        $data[1]['store_credit'] = '0';
        $data[1]['reward_points'] = '0';

        //Step 1
        $this->navigate('import');
        //Step 2
        $this->importExportHelper()->chooseImportOptions('Customer Finances', 'Add/Update Complex Data');
        //Step 3-5
        $report = $this->importExportHelper()->import($data);
        //Check import
        $this->assertArrayHasKey('import', $report, 'Import has been finished with issues: '
            . print_r($report, true));
        $this->assertArrayHasKey('success', $report['import'], 'Import has been finished with issues: '
            . print_r($report, true));
        //Step 6
        $this->navigate('manage_customers');
        //Step 7. First Customer
        $this->addParameter('customer_first_last_name', $userDataOne['first_name'] . ' ' . $userDataOne['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userDataOne['email']));
        //Verify customer account
        $this->assertEquals('$100.00', $this->customerHelper()->getStoreCreditBalance(),
            'Adding customer credit score balance is failed');
        $this->assertEquals('150', $this->customerHelper()->getRewardPointsBalance(),
            'Adding customer reward points balance is failed');
        //Step 7. Second Customer
        $this->navigate('manage_customers');
        $this->addParameter('customer_first_last_name', $userDataTwo['first_name'] . ' ' . $userDataTwo['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userDataTwo['email']));
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