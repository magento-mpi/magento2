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
 * Customer Address Export
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @method Enterprise2_Mage_CustomerAddressAttribute_Helper customerAddressAttributeHelper() customerAddressAttributeHelper()
 * @method Enterprise2_Mage_ImportExport_Helper importExportHelper() importExportHelper()
 */
class Enterprise2_Mage_ImportExport_CustomerAddress_AttributeTest extends Mage_Selenium_TestCase
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
     * <p>Add customer address attribute</p>
     * <p>Steps</p>
     * <p>1. Admin is logged in at backend</p>
     * <p>2. New Customers Address Attribute is created in Customers -> Attributes -> Manage Customers Address
     *    Attributes</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5611
     * @return array
     */
    public function addCustomerAddressAttribute()
    {
        //step1
        $this->admin('manage_customer_address_attributes');
        $attrData = $this->loadDataSet('ImportExport', 'generic_customer_address_attribute');
        $this->customerAddressAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');

        return $attrData;
    }

    /**
     * <p>Simple Export Address file with added address attribute (EE only)</p>
     *
     * <p>Preconditions:</p>
     * <p>1. Admin is logged in at frontend</p>
     * <p>2. Create new customer address attribute in Customers -> Attributes -> Manage Customer Address Attributes</p>
     * <p>3. Create new customer</p>
     *
     * <p>Steps</p>
     * <p>1. In System-> Import/Export-> Export select "Customers" entity type</p>
     * <p>2. Select "Magento2.0" format and "Address Type" file</p>
     * <p>3. Click on "Continue" button, save and open CSV file.</p>
     * <p>Expected: All customer addresses attributes are presented in opening file with new attribute from
     *    the precondition.</p>
     *
     * @test
     * @param array $attrData
     * @return array
     * @depends addCustomerAddressAttribute
     * @TestlinkId TL-MAGE-5611
     */
    public function simpleExportAddressFileWithCustomCustomerAddressAttribute($attrData)
    {
        //Precondition: create customer
        $this->navigate('manage_customers');
        $userData = $this->loadDataSet('ImportExport.yml', 'generic_customer_account');

        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        //Step 1
        $this->admin('export');
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file_version'));
        //Step 2.1
        $this->fillDropdown('export_file_version', 'Magento 2.0 format');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file'));
        //Step 2.2
        $this->fillDropdown('export_file', 'Customer Addresses');
        $this->waitForElementVisible($this->_getControlXpath('button', 'continue'));

        //Step 3
        $report = $this->ImportExportHelper()->export();
        //Verifying
        $this->assertNotNull($report, "Export csv file is empty");
        // search for new custom customer address attribute
        $this->assertArrayHasKey($attrData['attribute_code'], $report[0],
            'New custom customer address attribute is not present in export file'
        );

        return $attrData;
    }

    /**
     * <p>Simple Export Address file with added address attribute (EE only)</p>
     *
     * <p>Preconditions:</p>
     * <p>1. Admin is logged in at frontend</p>
     * <p>2. Create new customer address attribute in Customers -> Attributes -> Manage Customer Address Attributes</p>
     * <p>3. Delete the attribute from precondition 2 in Customers -> Attributes -> Manage Customer Address Attributes
     * </p>
     *
     * <p>Steps</p>
     * <p>1. In System-> Import/Export-> Export select "Customers" entity type</p>
     * <p>2. Select "Magento2.0" format and "Address Type" file</p>
     * <p>3. Click on "Continue" button, save and open CSV file.</p>
     * <p>Expected: All customer addresses attributes are presented in opening file except new deleted attribute from
     *    the precondition.</p>
     *
     * @test
     * @param array $attrData
     * @return array
     * @depends simpleExportAddressFileWithCustomCustomerAddressAttribute
     * @TestlinkId TL-MAGE-5501
     */
    public function simpleExportAddressFileWithDeletedCustomCustomerAddressAttribute($attrData)
    {
        //Precondition: delete custom address attribute
        $this->admin('manage_customer_address_attributes');
        $this->customerAddressAttributeHelper()->openAttribute(
            array(
                'attribute_code'=> $attrData['attribute_code'],
                'attribute_label'=> $attrData['attribute_label']
            ));
        //Delete attribute
        $this->clickButtonAndConfirm('delete_attribute', 'delete_confirm_message');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_attribute');

        //Step 1
        $this->admin('export');
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file_version'));
        //Step 2.1
        $this->fillDropdown('export_file_version', 'Magento 2.0 format');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file'));
        //Step 2.2
        $this->fillDropdown('export_file', 'Customer Addresses');
        $this->waitForElementVisible($this->_getControlXpath('button', 'continue'));

        //Step 3
        $report = $this->ImportExportHelper()->export();
        //Verifying
        $this->assertNotNull($report, "Export csv file is empty");
        // search for new custom customer address attribute
        $this->assertArrayNotHasKey($attrData['attribute_code'], $report[0],
            'Deleted custom customer address attribute is present in export file'
        );
    }
}
