<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iglazunova
 * Date: 6/18/12
 * Time: 3:51 PM
 * To change this template use File | Settings | File Templates.
 */
class Community2_Mage_ImportExport_CustomerValidationTest extends Mage_Selenium_TestCase
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
     * <p>Import File with not supported extensions</p>
     * <p>Steps</p>
     * <p>1. In System -> Import/ Export -> Import in drop-down "Entity Type" select "Customers"</p>
     * <p>2. Select "Append Complex Data" in selector "Import Behavior" </p>
     * <p>3. Select "Magento 2.0 format"</p>
     * <p>4. Select "Customers Main File"</p>
     * <p>5. Select .txt file in the are "File to Import"</p>
     * <p>Press "Check Data" button</p>
     * <p>Expected: Warning about incorrect file appears</p>
     *
     * @test
     * @dataProvider importData
     * @TestlinkId TL-MAGE-5613
     */
    public function importFileWithNotSupportedExtensions($data)
    {
        //Step 1
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'import_behavior'));
        //Step 2
        $this->fillDropdown('import_behavior', 'Append Complex Data');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'import_file_version'));
        //Step 3
        $this->fillDropdown('import_file_version', 'Magento 2.0 format');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'import_customer_entity'));
        $this->waitForElementVisible($this->_getControlXpath('field', 'file_to_import'));
        //Step 4
        $entityTypes = $this->importExportHelper()->getCustomerEntityType();
        foreach ($entityTypes as $entityType) {
            $this->fillDropdown('import_customer_entity', $entityType);
            //Step 5
            $report = $this->importExportHelper()->import($data,'example.pdf');
            $this->assertArrayNotHasKey('import', $report,
                'Incorrect file has been imported');
            $this->assertArrayHasKey('error', $report['validation'],
                'Error notification is missing on the Check Data');
        }
    }
    public function importData()
    {
        return array(
            array(array(array(
                'email' => 'test_email@never-domain.com',
                '_website' => 'base',
                '_store' => 'admin',
                'confirmation' => '',
                'created_at' => '01.06.2012 14:35',
                'created_in' => 'Admin',
                'default_billing' => '',
                'default_shipping' => '',
                'disable_auto_group_change' => '0',
                'dob' => '',
                'firstname' => 'first_name',
                'gender' => '',
                'group_id' => '1',
                'lastname' => 'last_name',
                'middlename' => '',
                'password_hash' => '48927b9ee38afb672504488a45c0719140769c24c10e5ba34d203ce5a9c15b27:2y',
                'website_id' => '0',
                'password' => ''
            )))
        );

    }
}