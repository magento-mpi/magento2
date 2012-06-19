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
 *
 * @method Enterprise2_Mage_CustomerAttribute_Helper customerAttributeHelper() customerAttributeHelper()
 * @method Enterprise2_Mage_CustomerAddressAttribute_Helper customerAddressAttributeHelper() customerAddressAttributeHelper()
 * @method Enterprise2_Mage_ImportExport_Helper importExportHelper() importExportHelper()
 */
class Community2_Mage_ImportExport_CustomerImportTest extends Mage_Selenium_TestCase
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
     * <p>Export Settings General View</p>
     * <p>Steps</p>
     * <p>1. Go to System -> Import/ Export -> Import</p>
     * <p>2. In the drop-down "Entity Type" select "Customers"</p>
     * <p>3. Select "New Import" fromat</p>
     * <p>Expected: dropdowns contain correct values</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5615
     */
    public function importSettingsGeneralView()
    {
        //Verifying
        $entityTypes = $this->getElementsByXpath(
            $this->_getControlXpath('dropdown', 'entity_type') . '/option',
            'text');
        $this->assertEquals(array(
                '-- Please Select --',
                'Products',
                'Customers'
            ), $entityTypes,
            'Entity Type dropdown contains incorrect values');
        $entityBehavior = $this->getElementsByXpath(
            $this->_getControlXpath('dropdown', 'import_behavior') . '/option',
            'text');
        $this->assertEquals(array(
                '-- Please Select --',
                'Append Complex Data',
                'Replace Existing Complex Data',
                'Delete Entities'
             ), $entityBehavior,
            'Import Behavior dropdown contains incorrect values');
        //Step 2
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'import_file_version'));
        //Verifying
        $exportFileVersion = $this->getElementsByXpath(
            $this->_getControlXpath('dropdown', 'import_file_version') . '/option',
            'text');
        $this->assertEquals(array('-- Please Select --', 'Magento 1.7 format', 'Magento 2.0 format'),
            $exportFileVersion,
            'Import File Version dropdown contains incorrect values');
        //Step 3
        $this->fillDropdown('import_file_version', 'Magento 2.0 format');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'import_customer_entity'));
        //Verifying
        $exportFileVersion = $this->getElementsByXpath(
            $this->_getControlXpath('dropdown', 'import_customer_entity') . '/option',
            'text');
        $this->assertEquals($this->importExportHelper()->getCustomerEntityType(),
            $exportFileVersion,
            'Customer Entity Type dropdown contains incorrect values');
        $this->assertTrue($this->controlIsVisible('field','file_to_import'),
            'File to Import field is missing');
    }

    /**
     * <p>Required columns</p>
     * <p>Steps</p>
     * <p>Go to System -> Import / Export -> Import</p>
     * <p>Select Entity Type: Customers</p>
     * <p>Select Export Format Version: Magento 2.0 format</p>
     * <p>Select Customers Entity Type: Customers Main File</p>
     * <p>Choose file from precondition</p>
     * <p>Click on Check Data</p>
     * <p>Click on Import button</p>
     * <p>Open Customers -> Manage Customers</p>
     * <p>Open each of imported customers</p>
     * <p>Expected: </p>
     * <p>After step 6</p>
     * <p>Verify that file is valid, the message 'File is valid!' is displayed</p>
     * <p>After step 7</p>
     * <p>Verify that import starting. The message 'Import successfully done.' is displayed</p>
     * <p>After step 8</p>
     * <p>Verify that imported customers display on customers grid</p>
     * <p>After step 9</p>
     * <p>Verify that all Customer information was imported</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5621
     */
    public function importWithRequiredColumns()
    {
        //Precondition: create 2 new customers
        $this->admin('manage_customers');
        // 0.1. create customer
        $customerData = $this->loadDataSet('ImportExport', 'generic_customer_required_fields');
        $this->customerHelper()->createCustomer($customerData);
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
        $this->fillDropdown('import_customer_entity', 'Customers Main File');
        //Generated CSV data
        $customerDataRow1 = $this->loadDataSet('ImportExport', 'import_main_file_required_fields',
            array('email' => $customerData['email']));
        $customerDataRow2 = $this->loadDataSet('ImportExport', 'import_main_file_required_fields',
            array(
                'email' => 'test_admin_' . $this->generate('string',5) . '@unknown-domain.com',
                'firstname' => 'first_' . $this->generate('string',10),
                'lastname' => 'last_' . $this->generate('string',10)
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
            $data[0]['firstname'] . ' ' . $data[0]['lastname']);
        $this->customerHelper()->openCustomer(
            array(
                'email' => strtolower($data[0]['email'])
            ));
        //Verify customer account
        $customerData['group'] = 'Retailer';
        $customerData['first_name'] = $customerDataRow1['firstname'];
        $customerData['last_name'] = $customerDataRow1['lastname'];
        $this->assertTrue($this->verifyForm($customerData, 'account_information'),
            'Existent customer has not been updated');
        //Verify customer account
        $this->admin('manage_customers');
        $this->addParameter('customer_first_last_name',
            $data[1]['firstname'] . ' ' . $data[1]['lastname']);
        $this->customerHelper()->openCustomer(
            array(
                'email' => strtolower($data[1]['email'])
            ));
        $customerData['group'] = 'Retailer';
        $customerData['email'] = strtolower($customerDataRow2['email']);
        $customerData['first_name'] = $customerDataRow2['firstname'];
        $customerData['last_name'] = $customerDataRow2['lastname'];
        $this->assertTrue($this->verifyForm($customerData, 'account_information'),
            'New customer has not been created');
    }

    /**
     * @dataProvider importData
     * @test
     */
    public function simpleImport($data)
    {
        //Step 1
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'import_behavior'));
        $this->fillDropdown('import_behavior', 'Append Complex Data');
        $this->waitForElementVisible($this->_getControlXpath('dropdown','import_file_version'));
        $this->fillDropdown('import_file_version', 'Magento 1.7 format');
        $report = $this->importExportHelper()->import($data);
    }

    public function importData()
    {
        return array(
            array(array(array(
                'email' => 'sdfsdf@qweqwe.cc',
                '_website' => 'base',
                '_store' => 'admin',
                'confirmation' => '',
                'created_at' => '01.06.2012 14:35',
                'created_in' => 'Admin',
                'default_billing' => '',
                'default_shipping' => '',
                'disable_auto_group_change' => '0',
                'dob' => '',
                'firstname' => 'sdfsdfsd',
                'gender' => '',
                'group_id' => '1',
                'lastname' => 'sdfsdfs',
                'middlename' => '',
                'password_hash' => '48927b9ee38afb672504488a45c0719140769c24c10e5ba34d203ce5a9c15b27:2y',
                'prefix' => '',
                'reward_update_notification' => '1',
                'reward_warning_notification' => '1',
                'rp_token' => '',
                'rp_token_created_at' => '',
                'store_id' => '0',
                'suffix' => '',
                'taxvat' => '',
                'website_id' => '0',
                'password' => ''
            )))
        );
    }
}
