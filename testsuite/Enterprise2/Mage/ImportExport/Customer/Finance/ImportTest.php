<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer Export
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_ImportExport_ImportFinanceTest extends Mage_Selenium_TestCase
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
        $this->navigate('export');
    }

    /**
     * <p>Required columns</p>
     * <p>Steps</p>
     * <p>1. Go to System -> Import / Export -> Import</p>
     * <p>2. Select Entity Type: Customers</p>
     * <p>3. Select Export Format Version: Magento 2.0 format</p>
     * <p>4. Select Customers Entity Type: Customer Finances File</p>
     * <p>5. Choose file from precondition</p>
     * <p>6. Click on Check Data</p>
     * <p>7. Click on Import button</p>
     * <p>8. Open Customers -> Manage Customers</p>
     * <p>9. Open each of imported customers</p>
     * <p>After step 6</p>
     * <p>Verify that file is valid, the message 'File is valid!' is displayed</p>
     * <p>After step 7</p>
     * <p>Verify that import starting. The message 'Import successfully done.' is displayed</p>
     * <p>After step 9</p>
     * <p>Verify that all Customers finance information was imported</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5624
     */
    public function importWithRequiredColumns()
    {
        //Precondition: create 2 new customers
        $this->admin('manage_customers');
        // 0.1. create two customers
        $this->navigate('manage_customers');
        $userData1 = $this->loadDataSet('ImportExport', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData1);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('manage_customers');
        $userData2 = $this->loadDataSet('ImportExport', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData2);
        $this->addParameter('customer_first_last_name', $userData2['first_name'] . ' ' . $userData2['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userData2['email']));
        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '1234'));
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => $userData2['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '4321'));
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->admin('import');
        //Step 1
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible(
            $this->_getControlXpath('dropdown', 'import_behavior')
        );
        $this->fillDropdown('import_behavior', 'Append Complex Data');
        $this->waitForElementVisible(
            $this->_getControlXpath('dropdown','import_file_version')
        );
        $this->fillDropdown('import_file_version', 'Magento 2.0 format');
        $this->waitForElementVisible(
            $this->_getControlXpath('dropdown', 'import_customer_entity')
        );
        $this->fillDropdown('import_customer_entity', 'Customer Finances');
        //Generated CSV data
        $customerDataRow1 = $this->loadDataSet('ImportExport', 'import_finance_file_required_fields',
            array(
                'email' => $userData1['email']
            ));
        $customerDataRow2 = $this->loadDataSet('ImportExport', 'import_finance_file_required_fields',
            array(
                'email' => $userData2['email'],
                'store_credit' => '4321.0000',
                'reward_points' => '1234',
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
            print_r($report) . print_r($data));
        $this->assertArrayHasKey('success', $report['import'], 'Import has been finished with issues:' .
            print_r($report) . print_r($data));
        //Check customers
        $this->admin('manage_customers');
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
        $this->admin('manage_customers');
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
     * <p>2. In the drop-down "Entity Type" select "Customers"</p>
     * <p>3. Select "Magento 2.0 format"</p>
     * <p>4. Select Customers Entity Type: Customer Finances File </p>
     * <p>5. Choose first file from precondition, click "Check Data" button, Press "Import" button</p>
     * <p>Expected: messages "Please fix errors and re-upload file or simply press "Import" button to skip rows with
     * errors" and "Checked rows: 2, checked entities: 2, invalid rows: 1, total errors: 1" are displayed</p>
     * <p>6. Open customers</p>
     * <p>Expected: valid finance data information was imported correctly</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5633
     */
    public function partialImport()
    {
        //Precondition
        $customerData = $this->loadDataSet('ImportExport.yml', 'data_valid_customer1_5635');
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($customerData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $csvData['valid'] = $this->loadDataSet('ImportExport.yml', 'csv_valid_finance_5633');
        $csvData['valid']['email'] = $customerData['email'];
        $csvData['invalid'] = $this->loadDataSet('ImportExport.yml', 'csv_invalid_finance_5633');
        $csvData['invalid']['email'] = $customerData['email'];
        //Step 1
        $this->admin('import');
        //Step 2
        $this->fillDropdown('entity_type', 'Customers');
        $this->fillDropdown('import_behavior', 'Append Complex Data');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'import_file_version'));
        //Step 3
        $this->fillDropdown('import_file_version', 'Magento 2.0 format');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'import_customer_entity'));
        //Step 4
        $this->fillDropdown('import_customer_entity', 'Customer Finances');
        //Step 5
        $importData = $this->importExportHelper()->import($csvData);
        //Verifying
        $this->assertEquals(
            'Please fix errors and re-upload file or simply press "Import" button to skip rows with errors  Import',
            $importData['validation']['validation'][0], 'No message about possibility to fix errors or continue'
        );
        $this->assertEquals(
            'Checked rows: 2, checked entities: 2, invalid rows: 1, total errors: 2',
            $importData['validation']['validation'][1], 'No message checked rows number'
        );
        //Verifying import
        $this->assertArrayHasKey('import', $importData, "Import has not been finished successfully");
        $this->assertArrayHasKey('success', $importData['import'], "Import has not been finished successfully");
        //Step 6
        $this->admin('manage_customers');
        $this->addParameter('customer_first_last_name', $customerData['first_name'] . ' '. $customerData['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $customerData['email']));
        //Verifying finance data
        $this->assertEquals('$' .$csvData['valid']['store_credit'] . '.00', $this->customerHelper()->getStoreCreditBalance(),
            'Store credit has not been added');
        $this->assertEquals($csvData['valid']['reward_points'], $this->customerHelper()->getRewardPointsBalance(),
            'Reward points have not been added');
    }
}