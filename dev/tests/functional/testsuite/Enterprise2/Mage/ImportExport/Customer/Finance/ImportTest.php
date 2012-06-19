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
class Enterprise2_Mage_ImportExport_CustomerFinanceTest extends Mage_Selenium_TestCase
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
     * <p>Go to System -> Import / Export -> Import</p>
     * <p>Select Entity Type: Customers</p>
     * <p>Select Export Format Version: Magento 2.0 format</p>
     * <p>Select Customers Entity Type: Customer Addresses File</p>
     * <p>Choose file from precondition</p>
     * <p>Click on Check Data</p>
     * <p>Click on Import button</p>
     * <p>Open Customers -> Manage Customers</p>
     * <p>Open each of imported customers</p>
     * <p>After step 6</p>
     * <p>Verify that file is valid, the message 'File is valid!' is displayed</p>
     * <p>After step 7</p>
     * <p>Verify that import starting. The message 'Import successfully done.' is displayed</p>
     * <p>After step 9</p>
     * <p>Verify that all Customers address information was imported</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5624
     */
    public function importWithRequiredColumns()
    {
        //Precondition: create 2 new customers
        $this->admin('manage_customers');
        // 0.1. create customers with/o address
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
        $this->fillDropdown('import_customer_entity', 'Customer Finances File');
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
        $this->assertEquals('$1,234.00', $this->customerHelper()->getStoreCreditBalance(),
            'Adding customer credit score balance is failed');
        $this->assertEquals('4321', $this->customerHelper()->getRewardPointsBalance(),
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
}