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
 * Customer Finances Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_ImportExport_ImportValidation_FinanceTest extends Mage_Selenium_TestCase
{
    protected static $customerEmail = NULL;

    public function setUpBeforeTests(){
        //logged in once for all tests
        $this->loginAdminUser();
        //Step 1
        $this->navigate('manage_customers');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        self::$customerEmail = $userData['email'];
    }
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
     * <p>Finances Import, if file data is invalid</p>
     * <p>Steps</p>
     * <p>Verify that import will not be started, if file has all rows that are invalid</p>
     * <p>Invalid row is:</p>
     * <p>a row with empty value of required attribute</p>
     * <p>a row with wrong value of some system attribute (non existing website_id or group_id)</p>
     * <p>a row with invalid values for attributes that pass validation (wrong format of email)</p>
     * <p>value format differs from attribute input type (some text value is present for attribute with type Yes/No)</p>
     * <p>if the required column is absent in import file (email, website, firstname, lastname, address_id, city, country_id, postcode, street, telephone), file is invalid</p>
     * <p>Press "Check Data" button</p>
     * <p>Expected: Warning about incorrect file appears</p>
     *
     * @test
     * @dataProvider importDataInvalid
     * @TestlinkId TL-MAGE-5643, TL-MAGE-5628
     */
    public function importFinanceFileWithInvalidData($financeData, array $validationMessage)
    {
        if (isset($financeData['_email']) && $financeData['_email']=='<realEmail>'){
            $financeData['_email'] = self::$customerEmail;
        }
        $this->navigate('import');
        //Step 1
        $this->importExportHelper()->chooseImportOptions('Customer Finances', 'Add/Update Complex Data');
        //Build CSV array
        $data = array(
            $financeData
        );
        //Import file
        $report = $this->importExportHelper()->import($data) ;
        //Check import
        $this->assertEquals($validationMessage, $report, 'Import has been finished with issues');
    }

    public function importDataInvalid()
    {
        $customerDataRow1 = $this->loadDataSet('ImportExport', 'generic_finance_csv',
            array(
                '_email' => ''
            ));
        $customerDataRow2 = $this->loadDataSet('ImportExport', 'generic_finance_csv',
            array(
                '_email' => '<realEmail>',
            ));
        unset($customerDataRow2['_website']);
        $customerDataRow3 = $this->loadDataSet('ImportExport', 'generic_finance_csv',
            array(
                '_email' => '<realEmail>',
                '_website' => 'notexist'
            ));
        $customerDataRow4 = $this->loadDataSet('ImportExport', 'generic_finance_csv',
            array(
                '_email' => '<realEmail>',
                'store_credit' => 'incorrect_value',
                'reward_points' => 'incorrect_value'
            ));
        $customerDataRow5 = $this->loadDataSet('ImportExport', 'generic_finance_csv',
            array(
                '_email' => '<realEmail>',
            ));
        unset($customerDataRow5['credit_score']);
        unset($customerDataRow5['store_points']);
        $customerDataRow6 = $this->loadDataSet('ImportExport', 'generic_finance_csv',
            array(
                '_email' => 'test_admin_' . $this->generate('string',5) . '@unknown-domain.com'
            ));

        return array(
            array($customerDataRow1, array('validation' => array(
                'error' => array(
                    "E-mail is not specified in rows: 1"
                    ),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")
                    )
                )
            ),
            array($customerDataRow2, array('validation' => array(
                'error' => array(
                    "Can not find required columns: _website"
                    ),
                'validation' => array(
                    "Please fix errors and re-upload file"
                    )
                    )
                )
            ),
            array($customerDataRow3, array('validation' => array(
                'error' => array(
                    "Invalid value in website column in rows: 1"
                    ),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")
                    )
                )
            ),
            array($customerDataRow4, array('validation' => array(
                'error' => array(
                    "Invalid value for 'store_credit' in rows: 1",
                    "Invalid value for 'reward_points' in rows: 1"
                    ),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 2")
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
                    )
                ,
                'import' => array(
                    'success' => array(
                        "Import successfully done."
                    )
                ))
            ),
            array($customerDataRow6, array('validation' => array(
                'error' => array(
                    "Customer with such email and website code doesn't exist in rows: 1"
                ),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")
            )
            )
            )
        );
    }

    /**
     * <p>Import File with duplicated rows</p>
     * <p>Precondition: csv files (main file, address file) prepared that contains two identical rows</p>
     * <p>Steps</p>
     * <p>1. In System -> Import/ Export -> Import in drop-down "Entity Type" select "Customers"</p>
     * <p>2. Select Entity Type: "Customers Finance"</p>
     * <p>3. Select Import Behavior: "Add/Update Complex Data"</p>
     * <p>4. Select customer main file from precondition</p>
     * <p>5. Press "Check Data" button</p>
     * <p>Expected: validation error 'E-mail is duplicated in import file in rows: X''</p>
     * <p>6. Add column '_action' with 'update' value to csv file</p>
     * <p>7. Repeat steps 4-5</p>
     *
     * @test
     * @dataProvider fileDuplicatedRows
     *
     * @TestlinkId TL-MAGE-5951
     */
    public function importFileWithDuplicatedRows($csv, $errorMessage)
    {
        // set email and address id in csv file
        foreach ($csv as $key => $value) {
            $csv[$key]['_email'] = self::$customerEmail;
        }

        //Step 1
        $this->navigate('import');
        //Steps 2-3
        $this->importExportHelper()->chooseImportOptions('Customer Finances', 'Add/Update Complex Data');
        //Steps 4-7
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
            $this->loadDataSet('ImportExport', 'generic_finance_csv'),
            $this->loadDataSet('ImportExport', 'generic_finance_csv')
        );
        $errorMessageMain = 'E-mail is duplicated in import file in rows: 2';

        return array(
            array( $csvMain, $errorMessageMain),
        );
    }

}