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
     * @dataProvider importDataFileName
     * @TestlinkId TL-MAGE-5613
     */
    public function importFileWithNotSupportedExtensions($dataFileName)
    {
        $customerDataRow = $this->loadDataSet('ImportExport', 'import_main_file_required_fields',
            array(
                'email' => 'test_admin_' . $this->generate('string',5) . '@unknown-domain.com',
                'firstname' => 'first_' . $this->generate('string',10),
                'lastname' => 'last_' . $this->generate('string',10)
            ));
        //Build CSV array
        $data = array(
            $customerDataRow
        );
        $entityTypes = $this->importExportHelper()->getCustomerEntityType();
        foreach ($entityTypes as $entityType) {
            $this->navigate('import');
            //Step 1
            $this->fillDropdown('entity_type', 'Customers');
            $this->waitForElementVisible($this->_getControlXpath('dropdown', 'import_behavior'));
            //Step 2
            $this->fillDropdown('import_behavior', 'Append Complex Data');
            $this->waitForElementVisible($this->_getControlXpath('dropdown', 'import_file_version'));
            //Step 3
            $this->fillDropdown('import_file_version', 'Magento 2.0 format');
            $this->waitForElementVisible($this->_getControlXpath('dropdown', 'import_customer_entity'));
            //Step 4
            $this->fillDropdown('import_customer_entity', $entityType);
            //Step 5
            $report = $this->importExportHelper()->import($data, $dataFileName);
            $this->assertArrayNotHasKey('import', $report,
                'Incorrect file has been imported');
            $this->assertArrayHasKey('error', $report['validation'],
                'Error notification is missing on the Check Data');
            foreach($report['validation']['error'] as $errorMessage)
                 $this->assertNotContains('Fatal error', $errorMessage,
                     'Fatal error is occurred');
            foreach($report['validation']['error'] as $errorMessage)
                $this->assertContains('Incorrect file type', $errorMessage,
                    'Incorrect file type message is not displayed');
        }
    }
    /**
     * <p>Customer Import, if file data is invalid</p>
     * <p>Steps</p>
     * <p>Verify that import will not be started, if file has all rows that are invalid</p>
     * <p>Invalid row is:</p>
     * <p>a row with empty value of required attribute</p>
     * <p>a row with wrong value of some system attribute (non existing website_id or group_id)</p>
     * <p>a row with invalid values for attributes that pass validation (wrong format of email)</p>
     * <p>value format differs from attribute input type (some text value is present for attribute with type Yes/No)</p>
     * <p>if the required column is absent in import file (email, website, firstname, group_id, lastname), file is invalid</p>
     * <p>Press "Check Data" button</p>
     * <p>Expected: Warning about incorrect file appears</p>
     *
     * @test
     * @dataProvider importDataInvalid
     * @TestlinkId TL-MAGE-5630
     */
    public function importCustomerFileWithInvalidData($customerData, array $validationMessage)
    {
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
        //Build CSV array
        $data = array(
            $customerData
        );
        //Import file
        $report = $this->importExportHelper()->import($data) ;
        //Check import
        $this->assertEquals($validationMessage, $report, 'Import has been finished with issues');
    }

    public function importDataFileName()
    {
        return array(
            array('example.pdf'),
            array('example.jpg')
        );

    }
    public function importDataInvalid()
    {
        $customerDataRow1 = $this->loadDataSet('ImportExport', 'import_main_file_required_fields',
            array(
                'email' => '',
                'firstname' => '',
                'lastname' => ''
            ));
        $customerDataRow2 = $this->loadDataSet('ImportExport', 'import_main_file_required_fields',
            array(
                'email' => 'test_admin_' . $this->generate('string',5) . '@unknown-domain.com',
                'lastname' => 'last_' . $this->generate('string',10)
            ));
        unset($customerDataRow2['firstname']);
        $customerDataRow3 = $this->loadDataSet('ImportExport', 'import_main_file_required_fields',
            array(
                '_website' => 'notexist',
                'email' => 'test_admin_' . $this->generate('string',5) . '@unknown-domain.com',
                'lastname' => 'last_' . $this->generate('string',10),
                'firstname' => 'last_' . $this->generate('string',10)
            ));
        $customerDataRow4 = $this->loadDataSet('ImportExport', 'import_main_file_required_fields',
            array(
                'email' => 'test_admin_' . $this->generate('string',5) . '@@unknown-domain.com',
                'lastname' => 'last_' . $this->generate('string',10),
                'firstname' => 'last_' . $this->generate('string',10)
            ));
        $customerDataRow5 = $this->loadDataSet('ImportExport', 'import_main_file_required_fields',
            array(
                'email' => 'test_admin_' . $this->generate('string',5) . '@unknown-domain.com',
                'lastname' => 'last_' . $this->generate('string',10),
                'firstname' => 'last_' . $this->generate('string',10),
            ));
        $customerDataRow5['disable_auto_group_change'] = '23123d123';
        return array(
            array($customerDataRow1, array('validation' => array(
                'error' => array(
                    "E-mail is invalid in rows: 1"
                ),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")
            )
            )
            ),
            array($customerDataRow2, array('validation' => array(
                'error' => array(
                    "Required attribute 'firstname' has an empty value in rows: 1"
                ),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")
            )
            )
            ),
            array($customerDataRow3, array('validation' => array(
                'error' => array(
                    "Invalid value in Website column (website does not exists?) in rows: 1"
                ),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")
            )
            )
            ),
            array($customerDataRow4, array('validation' => array(
                'error' => array(
                    "E-mail is invalid in rows: 1"
                ),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")
            )
            )
            ),
            array($customerDataRow5, array('validation' => array(
                'validation' => array(
                    "Checked rows: 1, checked entities: 1, invalid rows: 0, total errors: 0"
                ),
                'success' => array(
                    "File is valid! To start import process press \"Import\" button  Import"
                )
            ),
            'import' => array(
                'success' => array(
                    "Import successfully done."
                )
            )
            )
            )
        );
    }

}