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
class Community2_Mage_ImportExport_AddressImportTest extends Mage_Selenium_TestCase
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
        $userData2 = $this->loadDataSet('ImportExport', 'generic_customer_account');
        $addressData2 = $this->loadDataSet('ImportExport', 'generic_address');
        $this->customerHelper()->createCustomer($userData2, $addressData2);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('manage_customers');
        $userData1 = $this->loadDataSet('ImportExport', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData1);
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
        $this->fillDropdown('import_customer_entity', 'Customer Addresses File');
        //Generated CSV data
        $customerDataRow1 = $this->loadDataSet('ImportExport', 'import_address_file_required_fields1',
            array(
                '_email' => $userData1['email']
            ));
        $customerDataRow2 = $this->loadDataSet('ImportExport', 'import_address_file_required_fields2',
            array(
                '_email' => $userData2['email'],
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
        $this->openTab('addresses');
        $addressData1 = array();
        $addressData1['city'] = $data[1]['city'];
        $addressData1['first_name'] = $data[1]['firstname'];
        $addressData1['last_name'] = $data[1]['lastname'];
        $addressData1['zip_code'] = $data[1]['postcode'];
        $addressData1['street_address_line_1'] = $data[1]['street'];
        $addressData1['telephone'] = $data[1]['telephone'];
        //Verify customer account address
        $this->assertTrue($this->customerHelper()->isAddressPresent($addressData1),
            'New customer address has not been created');
        //Verify customer account
        $this->admin('manage_customers');
        $this->addParameter('customer_first_last_name',
            $userData2['first_name'] . ' ' . $userData2['last_name']);
        $this->customerHelper()->openCustomer(
            array(
                'email' => $userData2['email']
            ));
        $this->openTab('addresses');
        $addressData2['city'] = $data[1]['city'];
        $addressData2['first_name'] = $data[1]['firstname'];
        $addressData2['last_name'] = $data[1]['lastname'];
        $addressData2['zip_code'] = $data[1]['postcode'];
        $addressData2['street_address_line_1'] = $data[1]['street'];
        $addressData2['telephone'] = $data[1]['telephone'];
        $this->assertTrue($this->customerHelper()->isAddressPresent($addressData2),
            'Existent customer address has not been updated');
    }
}
