<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iglazunova
 * Date: 6/18/12
 * Time: 3:51 PM
 * To change this template use File | Settings | File Templates.
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
        //Step 1
        $this->navigate('import');
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
        if (isset($financeData['email']) && $financeData['email']=='<realEmail>'){
            $financeData['email'] = self::$customerEmail;
        }
        $this->admin('import');
        //Step 1
        $this->importExportHelper()->chooseImportOptions('Customers', 'Add/Update Complex Data',
            'Magento 2.0 format', 'Customer Finances');
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
                'email' => ''
            ));
        $customerDataRow2 = $this->loadDataSet('ImportExport', 'generic_finance_csv',
            array(
                'email' => '<realEmail>',
            ));
        unset($customerDataRow2['_website']);
        $customerDataRow3 = $this->loadDataSet('ImportExport', 'generic_finance_csv',
            array(
                'email' => '<realEmail>',
                '_website' => 'notexist'
            ));
        $customerDataRow4 = $this->loadDataSet('ImportExport', 'generic_finance_csv',
            array(
                'email' => '<realEmail>',
                'store_credit' => 'incorrect_value',
                'reward_points' => 'incorrect_value'
            ));
        $customerDataRow5 = $this->loadDataSet('ImportExport', 'generic_finance_csv',
            array(
                'email' => '<realEmail>',
            ));
        unset($customerDataRow5['credit_score']);
        unset($customerDataRow5['store_points']);
        $customerDataRow6 = $this->loadDataSet('ImportExport', 'generic_finance_csv',
            array(
                'email' => 'test_admin_' . $this->generate('string',5) . '@unknown-domain.com'
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

}