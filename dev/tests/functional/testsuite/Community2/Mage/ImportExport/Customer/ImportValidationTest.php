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
 * Customer Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_ImportExport_ImportValidation_CustomerTest extends Mage_Selenium_TestCase
{
    /**
     * Preconditions:
     * Log in to Backend.
     * Navigate to System -> Export/p>
     */
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        //Step 1
        $this->navigate('import');
    }
    /**
     * Import File with not supported extensions
     * Steps
     * 1. In System -> Import/ Export -> Import in drop-down "Entity Type" select "Customers Main File"
     * 2. Select "Add/Update Complex Data" in selector "Import Behavior" 
     * 3. Select .txt file in the are "File to Import"
     * Press "Check Data" button
     * Expected: Warning about incorrect file appears
     *
     * @test
     * @dataProvider importDataFileName
     * @TestlinkId TL-MAGE-5613
     */
    public function importFileWithNotSupportedExtensions($dataFileName)
    {
        $customerDataRow = $this->loadDataSet('ImportExport', 'generic_customer_csv',
            array(
                'email' => 'test_admin_' . $this->generate('string', 5) . '@unknown-domain.com',
                'firstname' => 'first_' . $this->generate('string', 10),
                'lastname' => 'last_' . $this->generate('string', 10)
            ));
        //Build CSV array
        $data = array(
            $customerDataRow
        );
        $entityTypes = $this->importExportHelper()->getCustomerEntityType();
        foreach ($entityTypes as $entityType) {
            $this->navigate('import');
            $this->importExportHelper()->chooseImportOptions($entityType, 'Add/Update Complex Data');
            $report = $this->importExportHelper()->import($data, $dataFileName);
            $this->assertArrayNotHasKey('import', $report,
                'Incorrect file has been imported');
            $this->assertArrayHasKey('error', $report['validation'],
                'Error notification is missing on the Check Data');
            foreach ($report['validation']['error'] as $errorMessage) {
                $this->assertNotContains('Fatal error', $errorMessage, 
                    'Fatal error is occurred');
            }
            foreach ($report['validation']['error'] as $errorMessage) {
                $this->assertContains('Incorrect file type', $errorMessage,
                    'Incorrect file type message is not displayed');
            }
        }
    }
    /**
     * Customer Import, if file data is invalid
     * Steps
     * Verify that import will not be started, if file has all rows that are invalid
     * Invalid row is:
     * a row with empty value of required attribute
     * a row with wrong value of some system attribute (non existing website_id or group_id)
     * a row with invalid values for attributes that pass validation (wrong format of email)
     * value format differs from attribute input type (some text value is present for attribute with type Yes/No)
     * if the required column is absent in import file (email, website, firstname, group_id, lastname), file is invalid
     * Press "Check Data" button
     * Expected: Warning about incorrect file appears
     *
     * @test
     * @dataProvider importDataInvalid
     * @TestlinkId TL-MAGE-5630
     */
    public function importCustomerFileWithInvalidData($customerData, array $validationMessage)
    {
        $this->navigate('import');
        //Step 1
        $this->importExportHelper()->chooseImportOptions('Customers Main File', 'Add/Update Complex Data');
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
        $customerCsv[0] = $this->loadDataSet('ImportExport', 'generic_customer_csv',
            array(
                'email' => '',
                'firstname' => '',
                'lastname' => ''
            ));
        $customerCsv[1] = $this->loadDataSet('ImportExport', 'generic_customer_csv',
            array(
                'email' => 'test_admin_' . $this->generate('string', 5) . '@unknown-domain.com',
                'lastname' => 'last_' . $this->generate('string', 10)
            ));
        unset($customerCsv[1]['firstname']);
        $customerCsv[2] = $this->loadDataSet('ImportExport', 'generic_customer_csv',
            array(
                '_website' => 'notexist',
                'email' => 'test_admin_' . $this->generate('string', 5) . '@unknown-domain.com',
                'lastname' => 'last_' . $this->generate('string', 10),
                'firstname' => 'last_' . $this->generate('string', 10)
            ));
        $customerCsv[3] = $this->loadDataSet('ImportExport', 'generic_customer_csv',
            array(
                'email' => 'test_admin_' . $this->generate('string', 5) . '@@unknown-domain.com',
                'lastname' => 'last_' . $this->generate('string', 10),
                'firstname' => 'last_' . $this->generate('string', 10)
            ));
        $customerCsv[4] = $this->loadDataSet('ImportExport', 'generic_customer_csv',
            array(
                'email' => 'test_admin_' . $this->generate('string', 5) . '@unknown-domain.com',
                'lastname' => 'last_' . $this->generate('string', 10),
                'firstname' => 'last_' . $this->generate('string', 10),
            ));
        $customerCsv[4]['disable_auto_group_change'] = '23123d123';
        return array(
            array($customerCsv[0], array('validation' => array(
                'error' => array(
                    "E-mail is not specified in rows: 1"
                ),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")
            )
            )
            ),
            array($customerCsv[1], array('validation' => array(
                'error' => array(
                    "Required attribute 'firstname' has an empty value in rows: 1"
                ),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")
            )
            )
            ),
            array($customerCsv[2], array('validation' => array(
                'error' => array(
                    "Invalid value in website column in rows: 1"
                ),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")
            )
            )
            ),
            array($customerCsv[3], array('validation' => array(
                'error' => array(
                    "E-mail is invalid in rows: 1"
                ),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")
            )
            )
            ),
            array($customerCsv[4], array('validation' => array(
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

    /**
     * Precondition for importFileWithDuplicatedRows()
     *
     * @test
     */
    public function duplicatedRowsPrecondition()
    {
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $addressData = $this->loadDataSet('Customers', 'generic_address');

        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData, $addressData);

        $this->assertMessagePresent('success', 'success_saved_customer');

        $this->addParameter('customer_first_last_name', $userData['first_name'] . ' ' . $userData['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userData['email']));
        $this->openTab('addresses');
        $addressData['entity_id'] = $this->customerHelper()->isAddressPresent($addressData);

        $userData['address'] = $addressData;

        return $userData;
    }

    /**
     * Import File with duplicated rows
     * Precondition: csv files (main file, address file) prepared that contains two identical rows
     * Steps
     * 1. In System -> Import/ Export -> Import in drop-down "Entity Type" select "Customers Main File"
     * 2. Select Add/Update Complex Data import behavior
     * 3. Select customer main file from precondition
     * 4. Press "Check Data" button
     * Expected: validation error 'E-mail is duplicated in import file in rows: X''
     * 5. Add column '_action' with 'update' value to csv file
     * 6. Repeat steps 3-4
     * 7. Repeat all steps for addresses file
     *
     * @test
     * @depends duplicatedRowsPrecondition
     * @dataProvider fileDuplicatedRows
     *
     * @TestlinkId TL-MAGE-5951
     */
    public function importFileWithDuplicatedRows($customerEntity, $csv, $errorMessage, $userData)
    {
        // set email and address id in csv file
        if ($customerEntity == 'Customers Main File') {
            foreach ($csv as $key => $value) {
                $csv[$key]['email'] = $userData['email'];
            }
        }
        if ($customerEntity == 'Customer Addresses') {
            foreach ($csv as $key => $value) {
                $csv[$key]['_email'] = $userData['email'];
                $csv[$key]['_entity_id'] = $userData['address']['entity_id'];
            }
        }

        //Steps 1-2
        $this->importExportHelper()->chooseImportOptions($customerEntity, 'Add/Update Complex Data');

        //Steps 3-7
        $csvCustomAction = $csv;
        foreach ($csvCustomAction as $key => $value) {
            $csvCustomAction[$key]['_action'] = 'update';
        }

        $csv = array($csv, $csvCustomAction);

        foreach ($csv as $value) {
            $report = $this->importExportHelper()->import($value);

            //Verifying
            $this->assertArrayHasKey('import', $report, 'Import is not done');
            $this->assertArrayHasKey('error', $report['validation'],
                'Error notification is missing on the Check Data');
            $this->assertContains($errorMessage, $report['validation']['error'][0],
                'Incorrect error message is displayed');
            $this->assertContains(
            "Please fix errors and re-upload file or simply press \"Import\" button to skip rows with errors  Import",
            $report['validation']['validation'][0], 'Wrong validation message is shown');
            $this->assertContains('Checked rows: 2, checked entities: 2, invalid rows: 1, total errors: 1',
                $report['validation']['validation'][1], 'Wrong message about checked rows');
        }
    }

    public function fileDuplicatedRows()
    {
        $csvMain = array(
            $this->loadDataSet('ImportExport', 'generic_customer_csv'),
            $this->loadDataSet('ImportExport', 'generic_customer_csv')
        );
        $errorMessageMain = 'E-mail is duplicated in import file in rows: 2';

        $csvAddress = array(
            $this->loadDataSet('ImportExport', 'generic_address_csv'),
            $this->loadDataSet('ImportExport', 'generic_address_csv'),
        );
        $errorMessageAddress = 'E-mail is duplicated in import file in rows: 2';

        return array(
            array('Customers Main File', $csvMain, $errorMessageMain),
            array('Customer Addresses', $csvAddress, $errorMessageAddress),
        );
    }
}