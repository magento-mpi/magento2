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
class Enterprise_Mage_ImportExport_Customer_Finance_EmptyValuesTest extends Mage_Selenium_TestCase
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
        $this->navigate('import');
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/enable_secret_key');
    }

    /**
     * Empty values for existing attributes in csv for Customer Finances
     *
     * @test
     * @dataProvider importData
     * @TestlinkId TL-MAGE-5644
     */
    public function emptyValuesAttributesInCsv($data)
    {
        //Create Customer1
        $this->navigate('manage_customers');
        $userDataEmpty[0] = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userDataEmpty[0]);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Create Customer2
        $this->navigate('manage_customers');
        $userDataEmpty[1] = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userDataEmpty[1]);
        $this->assertMessagePresent('success', 'success_saved_customer');

        //Update Customer 1
        $this->customerHelper()->openCustomer(array('email' => $userDataEmpty[0]['email']));

        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '100'));
        $userDataEmpty[0]['update_balance'] = '100';
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => $userDataEmpty[0]['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '150'));
        $userDataEmpty[0]['update_balance'] = '150';
        $this->assertMessagePresent('success', 'success_saved_customer');

        //Update Customer 2
        $this->customerHelper()->openCustomer(array('email' => $userDataEmpty[1]['email']));

        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '200'));
        $userDataEmpty[1]['update_balance'] = '200';
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => $userDataEmpty[1]['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '250'));
        $userDataEmpty[1]['update_balance'] = '250';
        $this->assertMessagePresent('success', 'success_saved_customer');

        $data[0]['_email'] = $userDataEmpty[0]['email'];
        $data[0]['store_credit'] = '';
        $data[0]['reward_points'] = '';

        $data[1]['_email'] = $userDataEmpty[1]['email'];
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
        $this->customerHelper()->openCustomer(array('email' => $userDataEmpty[0]['email']));
        //Verify customer account
        $this->assertEquals('$100.00', $this->customerHelper()->getStoreCreditBalance(),
            'Adding customer credit score balance is failed');
        $this->assertEquals('150', $this->customerHelper()->getRewardPointsBalance(),
            'Adding customer reward points balance is failed');
        //Step 7. Second Customer
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => $userDataEmpty[1]['email']));
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
            array(array(
                $this->loadDataSet('ImportExport', 'generic_finance_csv'),
                $this->loadDataSet('ImportExport', 'generic_finance_csv')
            ))
        );
    }
}