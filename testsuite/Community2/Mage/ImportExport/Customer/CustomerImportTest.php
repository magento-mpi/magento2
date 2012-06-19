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
     * <p>Validation Result block</p>
     * <p>Verify that Validation Result block will be displayed after checking data of import customer files</p>
     * <p>Precondition: at least one customer exists,
     * Customer Main, Address, Finance files must be generated after export</p>
     * <p>Steps</p>
     * <p>1. Go to System -> Import/ Export -> Import</p>
     * <p>2. In the drop-down "Entity Type" select "Customers"</p>
     * <p>3. Select "Magento 2.0 format"</p>
     * <p>4. Select Customers Entity Type: Customer Main File/Customer Addresses/Customer Finances</p>
     * <p>5. Select file to import</p>
     * <p>6. Click "Check Data" button.</p>
     * <p>Expected: validation and success messages are correct
     *
     * @test
     * @TestlinkId TL-MAGE-5618
     */
    public function mainValidationResultBlock()
    {
        //Precondition: create customer, add address
        $this->navigate('manage_customers');
        $userData = $this->loadDataSet('ImportExport.yml', 'generic_customer_account');
        $addressData = $this->loadDataSet('ImportExport.yml', 'generic_address');
        $this->customerHelper()->createCustomer($userData, $addressData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //add store credit and reward points (for EE)
        $customerTypes = $this->importExportHelper()->getCustomerEntityType();
        if (in_array('Customer Finances', $customerTypes)) {
            $this->addParameter('customer_first_last_name', $userData['first_name'] . ' ' . $userData['last_name']);
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            $this->customerHelper()->updateStoreCreditBalance(array('update_balance' =>'100'));
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            $this->customerHelper()->updateRewardPointsBalance(array('update_balance' =>'120'));
        }
        //export all customer files
        $this->admin('export');
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file_version'));
        $this->fillDropdown('export_file_version', 'Magento 2.0 format');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file'));
        $report = array();
        foreach ($customerTypes as $customerType) {
            $this->fillDropdown('export_file', $customerType);
            $this->waitForElementVisible($this->_getControlXpath('button', 'continue'));
            $report[$customerType] = $this->importExportHelper()->export();
        }
        //Step 1
        $this->admin('import');
        //Step 2
        $this->fillDropdown('entity_type', 'Customers');
        $this->fillDropdown('import_behavior', 'Append Complex Data');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'import_file_version'));
        //Step 3
        $this->fillDropdown('import_file_version', 'Magento 2.0 format');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'import_customer_entity'));
        foreach ($customerTypes as $customerType) {
            //Step 4
            $this->fillDropdown('import_customer_entity', $customerType);
            //Step 5-6
            $importData = $this->importExportHelper()->import($report[$customerType]);
            //Verifying
            $this->assertEquals('Checked rows: ' . count($report[$customerType]) . ', checked entities: '
                    . count($report[$customerType])
                    . ', invalid rows: 0, total errors: 0', $importData['validation']['validation'][0],
                'Validation message is not correct');
            $this->assertEquals('File is valid! To start import process press "Import" button  Import',
                $importData['validation']['success'][0], 'Success message is not correct');
        }
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
                '_website' => 'admin',
                '_store' => 'admin',
                'attr_ainalkiudfyisqgt' => '',
                'attr_hkuhj' => '',
                'attr_lqkkk' => '',
                'attr_ltavp' => '',
                'attr_sntecahafewtpbxo' => '',
                'attr_vzcmj' => '',
                'attr_xolge' => '',
                'attr_zsjyqshlvqousmdh' => '',
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
